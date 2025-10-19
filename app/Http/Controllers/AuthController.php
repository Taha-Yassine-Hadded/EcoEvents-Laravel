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
use Illuminate\Validation\Rules\Password;

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
            $validated = $request->validate([
                'name' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/'],
                'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users,email'],
                'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
                'phone' => ['nullable', 'string', 'regex:/^[+]?[0-9\s\-\(\)]{8,20}$/'],
                'city' => ['nullable', 'string', 'min:2', 'max:100', 'regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/'],

                'role' => ['required', 'in:user,organizer,sponsor'],
                'address' => ['nullable', 'string', 'min:5', 'max:255'],
                'bio' => ['nullable', 'string', 'min:10', 'max:1000'],
                'interests' => ['nullable', 'array', 'max:10'],
                'interests.*' => ['string', 'min:2', 'max:50', 'regex:/^[a-zA-ZÀ-ÿ\s\-\']+$/'],
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

            // The User model casts 'password' => 'hashed', so no manual hashing needed

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

            // Envoyer l'email de bienvenue
            try {
                Mail::to($user->email)->send(new WelcomeEmail($user));
            } catch (\Exception $e) {
                // Log l'erreur mais ne pas faire échouer l'inscription
                Log::error('Erreur envoi email de bienvenue: ' . $e->getMessage());
            }

            // Login user
            Auth::login($user);

            return redirect()->route('login')->with('success',
                'Votre compte a été créé avec succès ! Un email de bienvenue vous a été envoyé à ' . $user->email
            );
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de l\'inscription: ' . $e->getMessage()])->withInput();
        }
    }
}
