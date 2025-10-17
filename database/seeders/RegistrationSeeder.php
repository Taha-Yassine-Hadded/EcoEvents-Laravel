<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Database\Seeder;

class RegistrationSeeder extends Seeder
{
    public function run(): void
    {
        $participants = User::where('role', 'user')->get();
        $events = Event::all();

        foreach ($events as $event) {
            // Random participants for each event
            $randomParticipants = $participants->random(rand(5, 15));
            foreach ($randomParticipants as $participant) {
                Registration::create([
                    'event_id' => $event->id,
                    'user_id' => $participant->id,
                    'status' => 'registered',
                    'registered_at' => now(),
                ]);
            }
        }
    }
}
