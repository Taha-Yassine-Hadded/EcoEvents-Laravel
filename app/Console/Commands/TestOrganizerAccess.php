<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class TestOrganizerAccess extends Command
{
    protected $signature = 'test:organizer-access {email}';
    protected $description = 'Test organizer access to dashboard by generating a JWT token';

    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }
        
        if ($user->role !== 'organizer') {
            $this->error("User {$email} is not an organizer. Current role: {$user->role}");
            return 1;
        }
        
        try {
            $token = JWTAuth::fromUser($user);
            $this->info("JWT Token generated for organizer {$user->name} ({$user->email}):");
            $this->line($token);
            $this->newLine();
            $this->info("You can now test the dashboard access by:");
            $this->line("1. Opening browser developer tools");
            $this->line("2. Going to Application/Storage > Cookies");
            $this->line("3. Setting jwt_token cookie to: {$token}");
            $this->line("4. Navigate to /dashboard");
            
        } catch (\Exception $e) {
            $this->error("Error generating token: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}