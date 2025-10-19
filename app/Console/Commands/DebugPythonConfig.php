<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DebugPythonConfig extends Command
{
    protected $signature = 'debug:python-config';
    protected $description = 'Debug Python API configuration';

    public function handle()
    {
        $pythonUrl = env('PYTHON_API_URL');

        $this->info("🐍 PYTHON API DEBUG");
        $this->line("URL: " . ($pythonUrl ?: '❌ NON DÉFINIE'));
        $this->line("Raw env: " . getenv('PYTHON_API_URL'));

        if (!$pythonUrl) {
            $this->error("❌ PYTHON_API_URL manquante dans .env !");
            return 1;
        }

        // Test connexion
        try {
            $response = Http::timeout(5)->get(str_replace('/analyze-comment', '/health', $pythonUrl));
            $this->info("✅ Health check: " . $response->status());
        } catch (\Exception $e) {
            $this->error("💥 Health check KO: " . $e->getMessage());
        }

        $this->info("🌐 Test direct analyse...");
        $testData = ['content' => 'test', 'campaign_id' => 1, 'comment_id' => 1, 'user_id' => 1];

        try {
            $response = Http::timeout(10)->post($pythonUrl, $testData);
            $this->info("Status: " . $response->status());
            $this->info("Body: " . substr($response->body(), 0, 200));
        } catch (\Exception $e) {
            $this->error("Erreur: " . $e->getMessage());
        }

        return 0;
    }
}
