<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Community;
use App\Models\CommunityMember;
use App\Models\CommunityForumThread;
use App\Models\CommunityForumPost;

class CommunityForumThreadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Community $community)
    {
        $query = CommunityForumThread::query()
            ->where('community_id', $community->id)
            ->where('is_hidden', false)
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('content', 'like', "%$search%");
            });
        }

        $threads = $query->paginate(10);
        return response()->json([
            'success' => true,
            'community_id' => $community->id,
            'threads' => $threads,
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
    public function store(Request $request, Community $community)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
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
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'min:10'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
        ]);

        $thread = CommunityForumThread::create([
            'community_id' => $community->id,
            'user_id' => $user->id,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'tags' => $validated['tags'] ?? null,
        ]);

        return response()->json(['success' => true, 'thread' => $thread], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Community $community, CommunityForumThread $thread)
    {
        if ($thread->community_id !== $community->id) {
            return response()->json(['error' => 'Not found'], 404);
        }

        // Only return visible posts for public consumption
        $posts = CommunityForumPost::where('thread_id', $thread->id)
            ->where('is_hidden', false)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'thread' => $thread,
            'posts' => $posts,
        ]);
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
    public function update(Request $request, Community $community, CommunityForumThread $thread)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($thread->community_id !== $community->id) {
            return response()->json(['error' => 'Not found'], 404);
        }

        // Author or community organizer
        $isOrganizer = ($community->organizer_id ?? null) === $user->id;
        $isAuthor = $thread->user_id === $user->id;
        if (!$isOrganizer && !$isAuthor) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'content' => ['sometimes', 'string', 'min:10'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
        ]);

        $thread->fill($validated);
        $thread->save();

        return response()->json(['success' => true, 'thread' => $thread]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Community $community, CommunityForumThread $thread)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($thread->community_id !== $community->id) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $isOrganizer = ($community->organizer_id ?? null) === $user->id;
        $isAuthor = $thread->user_id === $user->id;
        if (!$isOrganizer && !$isAuthor) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $thread->delete();
        return response()->json(['success' => true]);
    }

    // Organizer-only actions
    public function pin(Community $community, CommunityForumThread $thread)
    {
        $user = Auth::user();
        if (!$user || ($community->organizer_id ?? null) !== $user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        if ($thread->community_id !== $community->id) {
            return response()->json(['error' => 'Not found'], 404);
        }
        $thread->update(['is_pinned' => true]);
        return response()->json(['success' => true, 'thread' => $thread]);
    }

    public function unpin(Community $community, CommunityForumThread $thread)
    {
        $user = Auth::user();
        if (!$user || ($community->organizer_id ?? null) !== $user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        if ($thread->community_id !== $community->id) {
            return response()->json(['error' => 'Not found'], 404);
        }
        $thread->update(['is_pinned' => false]);
        return response()->json(['success' => true, 'thread' => $thread]);
    }

    public function lock(Community $community, CommunityForumThread $thread)
    {
        $user = Auth::user();
        if (!$user || ($community->organizer_id ?? null) !== $user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        if ($thread->community_id !== $community->id) {
            return response()->json(['error' => 'Not found'], 404);
        }
        $thread->update(['is_locked' => true]);
        return response()->json(['success' => true, 'thread' => $thread]);
    }

    public function unlock(Community $community, CommunityForumThread $thread)
    {
        $user = Auth::user();
        if (!$user || ($community->organizer_id ?? null) !== $user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        if ($thread->community_id !== $community->id) {
            return response()->json(['error' => 'Not found'], 404);
        }
        $thread->update(['is_locked' => false]);
        return response()->json(['success' => true, 'thread' => $thread]);
    }

    public function hide(Community $community, CommunityForumThread $thread)
    {
        $user = Auth::user();
        if (!$user || ($community->organizer_id ?? null) !== $user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        if ($thread->community_id !== $community->id) {
            return response()->json(['error' => 'Not found'], 404);
        }
        $thread->update(['is_hidden' => true]);
        return response()->json(['success' => true, 'thread' => $thread]);
    }

    public function unhide(Community $community, CommunityForumThread $thread)
    {
        $user = Auth::user();
        if (!$user || ($community->organizer_id ?? null) !== $user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        if ($thread->community_id !== $community->id) {
            return response()->json(['error' => 'Not found'], 404);
        }
        $thread->update(['is_hidden' => false]);
        return response()->json(['success' => true, 'thread' => $thread]);
    }
}

