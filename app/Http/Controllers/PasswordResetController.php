<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Afficher le formulaire de demande de réinitialisation
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Envoyer le code de récupération par email
     */
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'L\'adresse email est requise.',
            'email.email' => 'Veuillez entrer une adresse email valide.',
            'email.exists' => 'Cette adresse email n\'existe pas dans notre système.'
        ]);

        $user = User::where('email', $request->email)->first();
        
        // Générer un code de 6 chiffres
        $resetCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Supprimer les anciens codes pour cet email
        PasswordReset::where('email', $request->email)->delete();
        
        // Créer un nouveau code de récupération
        PasswordReset::create([
            'email' => $request->email,
            'token' => $resetCode,
            'created_at' => Carbon::now()
        ]);

        // Envoyer l'email avec le code
        try {
            Mail::send('emails.password-reset', [
                'user' => $user,
                'resetCode' => $resetCode
            ], function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Code de récupération de mot de passe - EcoEvents');
            });

            // Stocker de façon persistante l'email pour les étapes suivantes
            session()->put('reset_email', $request->email);

            return redirect()->route('password.reset.verify')
                           ->with('success', 'Un code de récupération a été envoyé à votre adresse email.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'envoi de l\'email. Veuillez réessayer.');
        }
    }

    /**
     * Afficher le formulaire de vérification du code
     */
    public function showVerifyCodeForm()
    {
        if (!session('reset_email')) {
            return redirect()->route('password.request')
                           ->with('error', 'Session expirée. Veuillez recommencer.');
        }

        return view('auth.verify-reset-code');
    }

    /**
     * Vérifier le code de récupération
     */
    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6'
        ], [
            'code.required' => 'Le code de récupération est requis.',
            'code.size' => 'Le code doit contenir exactement 6 chiffres.'
        ]);

        // Sanitize code: keep only digits
        $sanitizedCode = preg_replace('/\D/', '', $request->code ?? '');

        $passwordReset = PasswordReset::where('email', $request->email)
                                    ->where('token', $sanitizedCode)
                                    ->first();

        if (!$passwordReset) {
            return back()->with('error', 'Code de récupération invalide.');
        }

        // Vérifier si le code a expiré (15 minutes)
        if (Carbon::parse($passwordReset->created_at)->addMinutes(15)->isPast()) {
            PasswordReset::where('email', $request->email)
                ->where('token', $sanitizedCode)
                ->delete();
            return back()->with('error', 'Le code de récupération a expiré. Veuillez en demander un nouveau.');
        }

        // Stocker l'email et le code de manière persistante en session
        session()->put('reset_email', $request->email);
        session()->put('reset_code', $sanitizedCode);

        return redirect()->route('password.reset.form')
                       ->with('success', 'Code vérifié avec succès. Vous pouvez maintenant réinitialiser votre mot de passe.');
    }

    /**
     * Afficher le formulaire de réinitialisation du mot de passe
     */
    public function showResetPasswordForm()
    {
        if (!session('reset_email') || !session('reset_code')) {
            return redirect()->route('password.request')
                           ->with('error', 'Session expirée. Veuillez recommencer.');
        }

        return view('auth.reset-password');
    }

    /**
     * Réinitialiser le mot de passe
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
            'password' => 'required|string|min:8|confirmed'
        ], [
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.'
        ]);

        // Vérifier à nouveau le code (sanitisation)
        $sanitizedCode = preg_replace('/\D/', '', $request->code ?? '');
        $passwordReset = PasswordReset::where('email', $request->email)
                                    ->where('token', $sanitizedCode)
                                    ->first();

        if (!$passwordReset) {
            return back()->with('error', 'Code de récupération invalide.');
        }

        // Vérifier l'expiration
        if (Carbon::parse($passwordReset->created_at)->addMinutes(15)->isPast()) {
            PasswordReset::where('email', $request->email)
                ->where('token', $sanitizedCode)
                ->delete();
            return redirect()->route('password.request')
                           ->with('error', 'Le code de récupération a expiré. Veuillez recommencer.');
        }

        // Mettre à jour le mot de passe
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Supprimer le code utilisé (table sans colonne id)
        PasswordReset::where('email', $request->email)
            ->where('token', $sanitizedCode)
            ->delete();

        // Nettoyer la session du flux de reset
        session()->forget(['reset_email', 'reset_code']);

        return redirect()->route('login')
                       ->with('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
    }
}
