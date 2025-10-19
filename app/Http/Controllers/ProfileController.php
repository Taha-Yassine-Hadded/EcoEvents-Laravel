<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    // Utile seulement si tu serres la page via la route ->view()
    public function edit(Request $request)
    {
        return view('pages.frontOffice.profile-edit', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'        => ['required','string','max:255'],
            'email'       => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'phone'       => ['nullable','string','max:30'],
            'address'     => ['nullable','string','max:255'],
            'city'        => ['nullable','string','max:100'],
            'bio'         => ['nullable','string','max:1000'],
            'interests'   => ['nullable','array'],
            'interests.*' => ['string','max:50'],
        ]);

        if (!empty($data['email']) && $data['email'] !== $user->email) {
            $user->email_verified_at = null;
        }
        if (!array_key_exists('interests', $data)) {
            $data['interests'] = [];
        }

        $user->fill($data)->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour',
            'user'    => $user->fresh(),
        ]);
    }

    public function updatePassword(Request $request)
    {
        // Validation simple (sans current_password rule lié à un guard)
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', Password::defaults(), 'confirmed'],
        ], [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
            'password.required'         => 'Le nouveau mot de passe est obligatoire.',
            'password.confirmed'        => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        $user = $request->user();

        // Vérifier le mot de passe actuel manuellement
        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response()->json([
                'message' => 'Le mot de passe actuel est incorrect.',
                'errors'  => ['current_password' => ['Le mot de passe actuel est incorrect.']],
            ], 422);
        }

        // Mettre à jour le mot de passe
        $user->forceFill([
            'password' => Hash::make($request->input('password')),
        ])->save();

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe mis à jour',
        ]);
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ]);

        $user = $request->user();

        if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->profile_image = $path;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Avatar mis à jour',
            'url'     => Storage::url($path),
        ]);
    }
}
