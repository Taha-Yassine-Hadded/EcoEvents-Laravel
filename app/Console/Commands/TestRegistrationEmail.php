<?php

namespace App\Console\Commands;

use App\Mail\EventRegistrationConfirmation;
use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestRegistrationEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:registration-email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the registration confirmation email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        // Find or create a test user
        $user = User::where('email', $email)->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Test User',
                'email' => $email,
                'password' => bcrypt('password'),
                'role' => 'user'
            ]);
            $this->info('Created test user: ' . $user->email);
        }
        
        // Find or create a test event
        $event = Event::first();
        if (!$event) {
            $this->error('No events found. Please create an event first.');
            return 1;
        }
        
        // Create a test registration
        $registration = Registration::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => 'registered',
            'registered_at' => now(),
            'role' => 'Guide - Accompagnement des participants',
            'skills' => 'Communication - Aisance relationnelle et expression',
            'has_transportation' => true,
            'has_participated_before' => false,
            'emergency_contact' => 'Test Contact - 1234567890',
        ]);
        
        try {
            // Send the email
            Mail::to($user->email)->send(new EventRegistrationConfirmation($user, $event, $registration));
            $this->info('Registration confirmation email sent successfully to: ' . $email);
            $this->info('Check your email inbox or Laravel logs for the email.');
            
            // Clean up test registration
            $registration->delete();
            $this->info('Test registration cleaned up.');
            
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}