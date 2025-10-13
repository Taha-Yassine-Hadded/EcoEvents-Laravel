<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Community;
use App\Models\CommunityMember;
use App\Models\CommunityForumThread;
use App\Models\CommunityForumPost;

class CommunityForumPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Community $community, CommunityForumThread $thread)
    {
        if ($thread->community_id !== $community->id) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $posts = CommunityForumPost::where('thread_id', $thread->id)
            ->where('is_hidden', false)
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'thread_id' => $thread->id,
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Community $community, CommunityForumThread $thread)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($thread->community_id !== $community->id) {
            return response()->json(['error' => 'Not found'], 404);
        }

        // Prevent posting in locked threads unless organizer
        $isOrganizer = ($community->organizer_id ?? null) === $user->id;
        if ($thread->is_locked && !$isOrganizer) {
            return response()->json(['error' => 'Thread is locked'], 423);
        }

        // Check approved membership
        $isMember = CommunityMember::where('community_id', $community->id)
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->exists();
        if (!$isMember) {
            return response()->json(['error' => 'Membership required'], 403);
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:2'],
        ]);

        $post = CommunityForumPost::create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'content' => $validated['content'],
        ]);

        return response()->json(['success' => true, 'post' => $post], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Community $community, CommunityForumThread $thread, CommunityForumPost $post)
    {
        if ($thread->community_id !== $community->id || $post->thread_id !== $thread->id) {
            return response()->json(['error' => 'Not found'], 404);
        }
        if ($post->is_hidden) {
            return response()->json(['error' => 'Not found'], 404);
        }
        return response()->json(['success' => true, 'post' => $post]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Community $community, CommunityForumThread $thread, CommunityForumPost $post)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        if ($thread->community_id !== $community->id || $post->thread_id !== $thread->id) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $isOrganizer = ($community->organizer_id ?? null) === $user->id;
        $isAuthor = $post->user_id === $user->id;
        if (!$isOrganizer && !$isAuthor) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:2'],
        ]);

        $post->content = $validated['content'];
        $post->save();

        return response()->json(['success' => true, 'post' => $post]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Community $community, CommunityForumThread $thread, CommunityForumPost $post)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        if ($thread->community_id !== $community->id || $post->thread_id !== $thread->id) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $isOrganizer = ($community->organizer_id ?? null) === $user->id;
        $isAuthor = $post->user_id === $user->id;
        if (!$isOrganizer && !$isAuthor) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $post->delete();
        return response()->json(['success' => true]);
    }
}

