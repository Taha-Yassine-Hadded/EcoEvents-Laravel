<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\ChatRoomMember;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ChatController extends Controller
{
    // Create a new custom private group
    public function createRoom(Request $request)
    {
        $user = Auth::user();
        // Only organizers can create groups via UI/API
        if (($user->role ?? null) !== 'organizer') {
            return response()->json(['error' => 'Only organizers can create rooms'], 403);
        }
        $data = $request->validate([
            'name' => ['nullable','string','max:100'],
            'member_ids' => ['array'],
            'member_ids.*' => ['integer','exists:users,id']
        ]);

        $room = ChatRoom::create([
            'owner_id' => $user->id,
            'target_type' => 'custom',
            'target_id' => null,
            'name' => $data['name'] ?? null,
            'is_private' => true,
        ]);

        // Add owner as admin
        ChatRoomMember::create([
            'chat_room_id' => $room->id,
            'user_id' => $user->id,
            'role' => 'admin',
        ]);

        // Add provided members (dedupe owner)
        $memberIds = collect($data['member_ids'] ?? [])->unique()->reject(fn($id)=>$id == $user->id);
        foreach ($memberIds as $mid) {
            ChatRoomMember::firstOrCreate([
                'chat_room_id' => $room->id,
                'user_id' => $mid,
            ], [ 'role' => 'member' ]);
        }

        return response()->json([
            'room' => $room->only(['id','name','is_private','target_type','target_id']),
            'members' => ChatRoomMember::where('chat_room_id', $room->id)
                ->with('user:id,name')
                ->get()
                ->map(fn($m)=>['id'=>$m->user->id,'name'=>$m->user->name,'role'=>$m->role]),
        ], 201);
    }

    // List rooms for current user (owner or member)
    public function myRooms(Request $request)
    {
        $user = Auth::user();
        $owned = ChatRoom::where('owner_id', $user->id)
            ->orderByDesc('last_activity_at')
            ->get(['id','name','is_private','target_type','target_id','created_at','last_message_id','last_activity_at']);
        $memberRoomIds = ChatRoomMember::where('user_id', $user->id)->pluck('chat_room_id');
        $memberRooms = ChatRoom::whereIn('id', $memberRoomIds)
            ->orderByDesc('last_activity_at')
            ->get(['id','name','is_private','target_type','target_id','created_at','last_message_id','last_activity_at']);
        $all = $owned->concat($memberRooms)->unique('id')->values();

        // Build payload with last message preview and unread_count for current user
        $rooms = $all->map(function($r) use ($user){
            $last = null;
            if ($r->last_message_id) {
                $m = ChatMessage::with('user:id,name')->find($r->last_message_id);
                if ($m) {
                    $last = [
                        'id' => $m->id,
                        'content' => mb_strimwidth($m->content, 0, 60, '…'),
                        'user' => ['id'=>$m->user->id, 'name'=>$m->user->name],
                        'created_at' => $m->created_at?->toIso8601String(),
                    ];
                }
            }
            if (!$last) {
                $m = ChatMessage::with('user:id,name')
                    ->where('chat_room_id', $r->id)
                    ->orderByDesc('id')
                    ->first();
                if ($m) {
                    $last = [
                        'id' => $m->id,
                        'content' => mb_strimwidth($m->content, 0, 60, '…'),
                        'user' => ['id'=>$m->user->id, 'name'=>$m->user->name],
                        'created_at' => $m->created_at?->toIso8601String(),
                    ];
                }
            }

            // Obtenir les informations de la communauté si c'est une room de communauté
            $communityInfo = null;
            if ($r->target_type === 'community' && $r->target_id) {
                $community = \App\Models\Community::find($r->target_id);
                if ($community) {
                    $communityInfo = [
                        'id' => $community->id,
                        'name' => $community->name,
                        'image' => $community->image_url ?: '/assets/images/default-community.png',
                    ];
                }
            }

            $member = ChatRoomMember::where('chat_room_id', $r->id)->where('user_id', $user->id)->first();
            $unread = $member?->unread_count ?? 0;

            return [
                'id' => $r->id,
                'name' => $r->name,
                'is_private' => (bool) $r->is_private,
                'target_type' => $r->target_type,
                'target_id' => $r->target_id,
                'last_activity_at' => $r->last_activity_at?->toIso8601String(),
                'last' => $last,
                'unread_count' => $unread,
                'community' => $communityInfo,
                'community_name' => $communityInfo['name'] ?? null,
                'community_image' => $communityInfo['image'] ?? null,
            ];
        });

        return response()->json(['rooms' => $rooms]);
    }

    // List recent messages
    public function history(ChatRoom $room, Request $request)
    {
        $user = Auth::user();
        // Authorization: must be owner or member
        if ($room->owner_id !== $user->id && !ChatRoomMember::where('chat_room_id', $room->id)->where('user_id', $user->id)->exists()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $messages = ChatMessage::with('user:id,name')
            ->where('chat_room_id', $room->id)
            ->orderByDesc('id')
            ->paginate(20);

        return response()->json([
            'messages' => [
                'data' => $messages->getCollection()->map(function($m){
                    return [
                        'id' => $m->id,
                        'content' => $m->content,
                        'user' => [ 'id' => $m->user->id, 'name' => $m->user->name ],
                        'created_at' => $m->created_at?->toIso8601String(),
                    ];
                }),
                'next_page_url' => $messages->nextPageUrl(),
                'prev_page_url' => $messages->previousPageUrl(),
            ]
        ]);
    }

    // Send a message to a room
    public function send(ChatRoom $room, Request $request)
    {
        $user = Auth::user();
        // Authorization: must be owner or member
        if ($room->owner_id !== $user->id && !ChatRoomMember::where('chat_room_id', $room->id)->where('user_id', $user->id)->exists()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'content' => ['required','string','min:1','max:2000'],
        ]);

        $message = ChatMessage::create([
            'chat_room_id' => $room->id,
            'user_id' => $user->id,
            'content' => $data['content'],
        ]);

        // Update room last message/activity
        $room->last_message_id = $message->id;
        $room->last_activity_at = now();
        $room->save();

        // Update unread counters: increment for other members, reset for sender
        ChatRoomMember::where('chat_room_id', $room->id)
            ->where('user_id', '!=', $user->id)
            ->increment('unread_count');
        ChatRoomMember::where('chat_room_id', $room->id)
            ->where('user_id', $user->id)
            ->update(['unread_count' => 0]);

        // Broadcast event
        event(new MessageSent($message->fresh('user:id,name')));

        return response()->json([
            'ok' => true,
            'message' => [
                'id' => $message->id,
                'content' => $message->content,
                'user' => [ 'id' => $message->user->id, 'name' => $message->user->name ],
                'created_at' => $message->created_at?->toIso8601String(),
            ]
        ], 201);
    }

    // Create or get a one-to-one room between current user and target user
    public function oneToOne(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'user_id' => ['required','integer','exists:users,id','different:auth_id'],
        ], [
            'different' => 'You cannot start a conversation with yourself.'
        ]);

        $targetId = (int) $request->input('user_id');

        // Find existing room with exactly these two members (including ownership)
        $existingIds = ChatRoomMember::where('user_id', $user->id)->pluck('chat_room_id');
        $room = ChatRoom::whereIn('id', $existingIds)
            ->where('is_private', true)
            ->where('target_type', 'custom')
            ->whereExists(function($q) use ($targetId){
                $q->from('chat_room_members as m2')
                  ->whereColumn('m2.chat_room_id', 'chat_rooms.id')
                  ->where('m2.user_id', $targetId);
            })
            ->first();

        if (!$room) {
            $room = ChatRoom::create([
                'owner_id' => $user->id,
                'target_type' => 'custom',
                'target_id' => null,
                'name' => null,
                'is_private' => true,
                'last_activity_at' => now(),
            ]);
            ChatRoomMember::firstOrCreate(['chat_room_id'=>$room->id,'user_id'=>$user->id],[ 'role'=>'admin' ]);
            ChatRoomMember::firstOrCreate(['chat_room_id'=>$room->id,'user_id'=>$targetId],[ 'role'=>'member' ]);
        }

        return response()->json(['room' => $room->only(['id'])]);
    }

    // Mark all messages in room as read for current user
    public function markRead(ChatRoom $room, Request $request)
    {
        $user = Auth::user();
        if ($room->owner_id !== $user->id && !ChatRoomMember::where('chat_room_id', $room->id)->where('user_id', $user->id)->exists()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        // Reset unread counter for the user
        ChatRoomMember::where('chat_room_id', $room->id)->where('user_id', $user->id)->update(['unread_count'=>0]);
        // Optionally set read_at on other users' messages newer than last view (simplified: mark latest N as read for this user)
        // This app doesn't track per-user read receipt per message yet. We'll skip detailed writes.
        return response()->json(['ok'=>true]);
    }

    // Typing indicator stub (no broadcast event in this iteration to keep minimal)
    public function typing(ChatRoom $room, Request $request)
    {
        $user = Auth::user();
        if ($room->owner_id !== $user->id && !ChatRoomMember::where('chat_room_id', $room->id)->where('user_id', $user->id)->exists()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $request->validate(['status' => ['required','in:start,stop']]);
        return response()->json(['ok'=>true]);
    }

    // List contacts (members from same communities by default; scope=all for global)
    public function contacts(Request $request)
    {
        $user = Auth::user();
        $scope = $request->query('scope', 'community');
        $search = trim((string)$request->query('search', ''));

        $query = User::query()->select('id','name');

        if ($scope !== 'all') {
            // Users sharing at least one community with current user
            $communityIds = DB::table('community_members')
                ->where('user_id', $user->id)
                ->pluck('community_id');
            $userIds = DB::table('community_members')
                ->whereIn('community_id', $communityIds)
                ->where('user_id', '!=', $user->id)
                ->pluck('user_id');
            $query->whereIn('id', $userIds);
        } else {
            $query->where('id', '!=', $user->id);
        }

        if ($search !== '') {
            $query->where('name', 'like', '%'.$search.'%');
        }

        $contacts = $query->orderBy('name')->limit(50)->get();
        return response()->json(['contacts' => $contacts]);
    }
}
