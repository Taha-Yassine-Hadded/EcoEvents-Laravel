<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SponsorProfileController extends Controller
{
    /**
     * Afficher le profil du sponsor
     */
    public function show(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return redirect()->route('login')->with('error', 'Accès non autorisé.');
        }

        return view('pages.backOffice.sponsor-profile', compact('user'));
    }

    /**
     * Mettre à jour le profil du sponsor
     */
    public function update(Request $request)
    {
        try {
            $user = $request->auth;
            
            if (!$user || $user->role !== 'sponsor') {
                return response()->json(['error' => 'Accès non autorisé.'], 401);
            }

        // Validation flexible - seulement valider les champs présents
        $rules = [];
        
        if ($request->has('name') && !empty($request->name)) {
            $rules['name'] = 'string|max:255';
        }
        
        if ($request->has('email') && !empty($request->email)) {
            $rules['email'] = 'email|unique:users,email,' . $user->id;
        }
        
        if ($request->has('company_name')) {
            $rules['company_name'] = 'nullable|string|max:255';
        }
        
        if ($request->has('phone')) {
            $rules['phone'] = 'nullable|string|max:30';
        }
        
        if ($request->has('address')) {
            $rules['address'] = 'nullable|string|max:255';
        }
        
        if ($request->has('city')) {
            $rules['city'] = 'nullable|string|max:100';
        }
        
        if ($request->has('bio')) {
            $rules['bio'] = 'nullable|string|max:1000';
        }
        
        if ($request->has('budget')) {
            $rules['budget'] = 'nullable|numeric|min:0|max:999999.99';
        }
        
        if ($request->has('sector')) {
            $rules['sector'] = 'nullable|string|max:50';
        }
        
        if ($request->hasFile('profile_image')) {
            $rules['profile_image'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
        }
        
        // Valider seulement si il y a des règles
        if (!empty($rules)) {
            $request->validate($rules);
        }

        // Préparer les données à mettre à jour - seulement les champs présents
        $data = [];
        
        if ($request->has('name') && !empty($request->name)) {
            $data['name'] = $request->name;
        }
        
        if ($request->has('email') && !empty($request->email)) {
            $data['email'] = $request->email;
        }
        
        if ($request->has('company_name')) {
            $data['company_name'] = $request->company_name;
        }
        
        if ($request->has('phone')) {
            $data['phone'] = $request->phone;
        }
        
        if ($request->has('address')) {
            $data['address'] = $request->address;
        }
        
        if ($request->has('city')) {
            $data['city'] = $request->city;
        }
        
        if ($request->has('bio')) {
            $data['bio'] = $request->bio;
        }
        
        if ($request->has('budget')) {
            $data['budget'] = $request->budget;
        }
        
        if ($request->has('sector')) {
            $data['sector'] = $request->sector;
        }

        // Gestion de l'image de profil
        if ($request->hasFile('profile_image')) {
            // Supprimer l'ancienne image si elle existe
            if ($user->profile_image && Storage::exists('public/' . $user->profile_image)) {
                Storage::delete('public/' . $user->profile_image);
            }

            $image = $request->file('profile_image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('public/profile_images', $imageName);
            $data['profile_image'] = 'profile_images/' . $imageName;
        }

        // Mettre à jour seulement si il y a des données
        if (!empty($data)) {
            $user->update($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès.',
            'user' => $user->fresh(),
            'updated_fields' => array_keys($data)
        ]);
        
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('SponsorProfileController: Erreur update', [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du profil.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Le mot de passe actuel est incorrect.'], 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe mis à jour avec succès.'
        ]);
    }
}