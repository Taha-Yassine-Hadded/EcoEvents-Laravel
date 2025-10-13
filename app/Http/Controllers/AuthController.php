<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\WelcomeEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\Rules\Password;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegisterForm()
    {
        try {
            return view('auth.register');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        try {
            // Provide a default role when not provided
            if (!$request->has('role') || empty($request->input('role'))) {
                $request->merge(['role' => 'user']);
            }

            $validated = $request->validate([
                'name' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/'],
                'email' => ['required', 'string', 'email:rfc', 'max:255', 'unique:users,email'],
                'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
                'phone' => ['nullable', 'string', 'regex:/^[+]?[0-9\s\-\(\)]{8,20}$/'],
                'city' => ['nullable', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/'],
                'role' => ['in:user,organizer'],
                'address' => ['nullable', 'string', 'min:5', 'max:255'],
                'bio' => ['nullable', 'string', 'min:10', 'max:1000'],
                'interests' => ['nullable', 'array', 'max:10'],
                // Autoriser lettres, espaces, tirets, apostrophes et underscores (les valeurs de la vue utilisent des underscores)
                'interests.*' => ['string', 'min:2', 'max:50', 'regex:/^[a-zA-ZÀ-ÿ\s\-_\']+$/'],
                'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048', 'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'],
            ], [
                'name.required' => 'Le nom est obligatoire.',
                'name.min' => 'Le nom doit contenir au moins 2 caractères.',
                'name.regex' => 'Le nom ne peut contenir que des lettres, espaces, tirets et apostrophes.',
                'email.required' => 'L\'adresse email est obligatoire.',
                'email.email' => 'Veuillez saisir une adresse email valide.',
                'email.unique' => 'Cette adresse email est déjà utilisée.',
                'password.required' => 'Le mot de passe est obligatoire.',
                'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
                'phone.regex' => 'Le numéro de téléphone n\'est pas valide.',
                'city.regex' => 'La ville ne peut contenir que des lettres, espaces, tirets et apostrophes.',
                'address.min' => 'L\'adresse doit contenir au moins 5 caractères.',
                'bio.min' => 'La biographie doit contenir au moins 10 caractères.',
                'bio.max' => 'La biographie ne peut pas dépasser 1000 caractères.',
                'interests.max' => 'Vous ne pouvez pas sélectionner plus de 10 centres d\'intérêt.',
                'interests.*.regex' => 'Les centres d\'intérêt ne peuvent contenir que des lettres, espaces, tirets et apostrophes.',
                'profile_image.image' => 'Le fichier doit être une image.',
                'profile_image.max' => 'L\'image ne peut pas dépasser 2 Mo.',
                'profile_image.dimensions' => 'L\'image doit faire au minimum 100x100 pixels et au maximum 2000x2000 pixels.',
            ]);

            // Default role fallback if not provided (defensive)
            if (empty($validated['role'])) {
                $validated['role'] = 'user';
            }

            // The User model casts 'password' => 'hashed', so no manual hashing needed

            Log::info('Register: validated payload', [
                'email' => $validated['email'] ?? null,
                'role' => $validated['role'] ?? null,
                'has_profile_image' => $request->hasFile('profile_image'),
            ]);

            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                $path = $request->file('profile_image')->store('profiles', 'public');
                $validated['profile_image'] = $path;
            }

            // Normalize interests to array (json cast in model)
            if (!empty($validated['interests']) && is_array($validated['interests'])) {
                $validated['interests'] = array_values($validated['interests']);
            }

            // Create user
            $user = User::create($validated);
            Log::info('Register: user created', ['id' => $user->id, 'email' => $user->email]);

            // Envoyer l'email de bienvenue
            try {
                Mail::to($user->email)->send(new WelcomeEmail($user));
            } catch (\Exception $e) {
                // Log l'erreur mais ne pas faire échouer l'inscription
            }

            // Login user (session) + issue JWT for middleware VerifyJWT
            Auth::login($user);
            $token = JWTAuth::fromUser($user);

            // Set JWT in cookie for 7 days, HttpOnly
            $cookie = cookie(
                'jwt_token',
                $token,
                60 * 24 * 7, // minutes
                '/',
                null,
                false, // secure (set to true if HTTPS)
                true,  // httpOnly
                false,
                'Lax'
            );

            // Force server-side redirect to a public page (Option A)
            return redirect()->route('home')
                ->with('success', 'Votre compte a été créé avec succès !')
                ->cookie($cookie);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $ve->errors()], 422);
            }
            return back()->withErrors($ve->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Register: exception', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Erreur lors de l\'inscription: ' . $e->getMessage()], 500);
            }
            return back()->withErrors(['error' => 'Erreur lors de l\'inscription: ' . $e->getMessage()])->withInput();
        }
    }
}
