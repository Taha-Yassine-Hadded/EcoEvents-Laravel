<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SponsorProfileController extends Controller
{
    public function show(Request $request)
    {
        try {
            $user = $request->auth;
            
            if (!$user || $user->role !== 'sponsor') {
                return response()->json(['error' => 'Accès non autorisé.'], 401);
            }

            return view('pages.backOffice.sponsor-profile', [
                'user' => $user
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SponsorProfileController: Erreur show', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur lors du chargement du profil.'], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $user = $request->auth;
            
            if (!$user || $user->role !== 'sponsor') {
                return response()->json(['error' => 'Accès non autorisé.'], 401);
            }

            // Validation flexible - seulement les champs fournis
            $rules = [];
            $updateData = [];
            
            if ($request->has('name') && !empty($request->name)) {
                $rules['name'] = 'string|max:255';
                $updateData['name'] = $request->name;
            }
            
            if ($request->has('email') && !empty($request->email)) {
                $rules['email'] = 'email|unique:users,email,' . $user->id;
                $updateData['email'] = $request->email;
            }
            
            if ($request->has('phone')) {
                $rules['phone'] = 'nullable|string|max:20';
                $updateData['phone'] = $request->phone;
            }
            
            if ($request->has('address')) {
                $rules['address'] = 'nullable|string|max:255';
                $updateData['address'] = $request->address;
            }
            
            if ($request->has('city')) {
                $rules['city'] = 'nullable|string|max:100';
                $updateData['city'] = $request->city;
            }
            
            if ($request->has('bio')) {
                $rules['bio'] = 'nullable|string|max:1000';
                $updateData['bio'] = $request->bio;
            }
            
            if ($request->hasFile('profile_image')) {
                $rules['profile_image'] = 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048';
            }
            
            // Valider seulement si il y a des règles
            if (!empty($rules)) {
                $request->validate($rules);
            }

            // Gérer l'upload de l'image de profil
            if ($request->hasFile('profile_image')) {
                // Supprimer l'ancienne image si elle existe
                if ($user->profile_image) {
                    Storage::disk('public')->delete($user->profile_image);
                }
                
                $updateData['profile_image'] = $request->file('profile_image')->store('profile-images', 'public');
            }

            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Profil mis à jour avec succès !',
                'user' => $user
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SponsorProfileController: Erreur update', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur lors de la mise à jour du profil.'], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $user = $request->auth;
            
            if (!$user || $user->role !== 'sponsor') {
                return response()->json(['error' => 'Accès non autorisé.'], 401);
            }

            $validated = $request->validate([
                'current_password' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // Vérifier le mot de passe actuel
            if (!Hash::check($validated['current_password'], $user->password)) {
                return response()->json(['error' => 'Le mot de passe actuel est incorrect.'], 422);
            }

            $user->update([
                'password' => Hash::make($validated['password'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe mis à jour avec succès !'
            ]);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SponsorProfileController: Erreur updatePassword', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur lors de la mise à jour du mot de passe.'], 500);
        }
    }
}
