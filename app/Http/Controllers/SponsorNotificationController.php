<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SponsorNotification;
use App\Models\NotificationPreference;
use App\Services\NotificationService;

class SponsorNotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Afficher la page des notifications
     */
    public function showNotificationsPage(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return redirect()->route('login')->with('error', 'Accès non autorisé.');
        }

        return view('pages.backOffice.sponsor-notifications', compact('user'));
    }

    /**
     * Obtenir les notifications de l'utilisateur
     */
    public function index(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        $notifications = SponsorNotification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    /**
     * Obtenir les notifications non lues
     */
    public function unread(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        $notifications = $this->notificationService->getUnreadNotifications($user);

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $notifications->count()
        ]);
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead(Request $request, $id)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        $notification = SponsorNotification::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$notification) {
            return response()->json(['error' => 'Notification non trouvée.'], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marquée comme lue.'
        ]);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        $this->notificationService->markAllAsRead($user);

        return response()->json([
            'success' => true,
            'message' => 'Toutes les notifications ont été marquées comme lues.'
        ]);
    }

    /**
     * Obtenir les préférences de notification
     */
    public function getPreferences(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        $preferences = NotificationPreference::where('user_id', $user->id)->get();

        return response()->json([
            'success' => true,
            'preferences' => $preferences,
            'available_types' => NotificationPreference::getAvailableTypes()
        ]);
    }

    /**
     * Mettre à jour les préférences de notification
     */
    public function updatePreferences(Request $request)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        $request->validate([
            'preferences' => 'required|array',
            'preferences.*.notification_type' => 'required|string',
            'preferences.*.email_enabled' => 'boolean',
            'preferences.*.sms_enabled' => 'boolean',
            'preferences.*.push_enabled' => 'boolean',
            'preferences.*.in_app_enabled' => 'boolean',
            'preferences.*.timing_preferences' => 'nullable|array'
        ]);

        foreach ($request->preferences as $pref) {
            NotificationPreference::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'notification_type' => $pref['notification_type']
                ],
                [
                    'email_enabled' => $pref['email_enabled'] ?? true,
                    'sms_enabled' => $pref['sms_enabled'] ?? false,
                    'push_enabled' => $pref['push_enabled'] ?? true,
                    'in_app_enabled' => $pref['in_app_enabled'] ?? true,
                    'timing_preferences' => $pref['timing_preferences'] ?? null
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Préférences mises à jour avec succès.'
        ]);
    }

    /**
     * Supprimer une notification
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->auth;
        
        if (!$user || $user->role !== 'sponsor') {
            return response()->json(['error' => 'Accès non autorisé.'], 401);
        }

        $notification = SponsorNotification::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$notification) {
            return response()->json(['error' => 'Notification non trouvée.'], 404);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification supprimée.'
        ]);
    }
}