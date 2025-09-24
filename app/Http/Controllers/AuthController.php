<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'confirmed', 'min:8'],
                'phone' => ['nullable', 'string', 'max:20'],
                'city' => ['nullable', 'string', 'max:100'],
                'role' => ['required', 'in:user,organizer'],
                'address' => ['nullable', 'string', 'max:255'],
                'bio' => ['nullable', 'string', 'max:1000'],
                'interests' => ['nullable', 'array'],
                'interests.*' => ['string', 'max:100'],
                'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            ]);

            // Hash password
            $validated['password'] = Hash::make($validated['password']);

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

            // Login user
            Auth::login($user);

            return redirect()->route('home')->with('success', 'Votre compte a été créé avec succès.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erreur lors de l\'inscription: ' . $e->getMessage()])->withInput();
        }
    }
}
