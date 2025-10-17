<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\CommunityMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommunityMembershipController extends Controller
{
    /**
     * Rejoindre une communauté (envoyer une demande)
     */
    public function join(Community $community)
    {
        $user = Auth::user();
        
        // Vérifier si l'utilisateur est déjà membre
        $existingMember = CommunityMember::where('community_id', $community->id)
                                        ->where('user_id', $user->id)
                                        ->first();
        
        if ($existingMember) {
            if ($existingMember->status === 'approved') {
                return redirect()->back()->with('error', 'Vous êtes déjà membre de cette communauté.');
            } elseif ($existingMember->status === 'pending') {
                return redirect()->back()->with('error', 'Votre demande est déjà en cours de traitement.');
            }
        }
        
        // Vérifier si la communauté est pleine
        if ($community->isFull()) {
            return redirect()->back()->with('error', 'Cette communauté a atteint sa capacité maximale.');
        }
        
        // Créer ou mettre à jour la demande
        CommunityMember::updateOrCreate(
            [
                'community_id' => $community->id,
                'user_id' => $user->id
            ],
            [
                'status' => 'pending',
                'joined_at' => null
            ]
        );
        
        return redirect()->back()->with('success', 'Votre demande d\'adhésion a été envoyée à l\'organisateur.');
    }
    
    /**
     * Quitter une communauté
     */
    public function leave(Community $community)
    {
        $user = Auth::user();
        
        $membership = CommunityMember::where('community_id', $community->id)
                                   ->where('user_id', $user->id)
                                   ->first();
        
        if (!$membership) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas membre de cette communauté.');
        }
        
        $membership->delete();
        
        return redirect()->back()->with('success', 'Vous avez quitté la communauté avec succès.');
    }
    
    /**
     * Approuver une demande d'adhésion (organisateur)
     */
    public function approve(Community $community, $userId)
    {
        // Vérifier que l'utilisateur connecté est l'organisateur
        if (Auth::id() !== $community->organizer_id) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à effectuer cette action.');
        }
        
        $membership = CommunityMember::where('community_id', $community->id)
                                   ->where('user_id', $userId)
                                   ->where('status', 'pending')
                                   ->first();
        
        if (!$membership) {
            return redirect()->back()->with('error', 'Demande introuvable.');
        }
        
        // Vérifier si la communauté est pleine
        if ($community->isFull()) {
            return redirect()->back()->with('error', 'La communauté a atteint sa capacité maximale.');
        }
        
        $membership->update([
            'status' => 'approved',
            'joined_at' => now()
        ]);
        
        return redirect()->back()->with('success', 'Demande approuvée avec succès.');
    }
    
    /**
     * Refuser une demande d'adhésion (organisateur)
     */
    public function reject(Community $community, $userId)
    {
        // Vérifier que l'utilisateur connecté est l'organisateur
        if (Auth::id() !== $community->organizer_id) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à effectuer cette action.');
        }
        
        $membership = CommunityMember::where('community_id', $community->id)
                                   ->where('user_id', $userId)
                                   ->where('status', 'pending')
                                   ->first();
        
        if (!$membership) {
            return redirect()->back()->with('error', 'Demande introuvable.');
        }
        
        $membership->delete();
        
        return redirect()->back()->with('success', 'Demande refusée.');
    }
}
