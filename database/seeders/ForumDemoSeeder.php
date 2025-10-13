<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Community;
use App\Models\User;
use App\Models\CommunityMember;
use App\Models\CommunityForumThread;
use App\Models\CommunityForumPost;

class ForumDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use transaction to keep DB consistent for each community processed
        $communities = Community::query()->inRandomOrder()->take(5)->get();
        $users = User::query()->inRandomOrder()->take(20)->get();

        if ($communities->isEmpty() || $users->isEmpty()) {
            Log::info('[ForumDemoSeeder] Skipped: need existing communities and users.');
            return;
        }

        foreach ($communities as $community) {
            DB::transaction(function () use ($community, $users) {
                // Pick a subset of users to participate in this community
                $participants = $users->shuffle()->take(rand(5, min(12, $users->count())));

                // Ensure membership approved for participants
                foreach ($participants as $u) {
                    CommunityMember::updateOrCreate(
                        [
                            'community_id' => $community->id,
                            'user_id' => $u->id,
                        ],
                        [
                            'status' => 'approved',
                            'is_active' => true,
                            'joined_at' => now(),
                        ]
                    );
                }

                // Create threads (3-5 per community)
                $threadCount = rand(3, 5);
                for ($i = 0; $i < $threadCount; $i++) {
                    $author = $participants->random();
                    $thread = CommunityForumThread::factory()
                        ->for($community, 'community')
                        ->for($author, 'user')
                        ->create();

                    // Create posts (5-10 per thread)
                    $postCount = rand(5, 10);
                    for ($j = 0; $j < $postCount; $j++) {
                        $poster = $participants->random();
                        CommunityForumPost::factory()
                            ->for($thread, 'thread')
                            ->for($poster, 'user')
                            ->create();
                    }
                }
            });
        }
    }
}
