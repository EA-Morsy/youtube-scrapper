<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->baseUrl = 'https://api.openai.com/v1';
    }

    /**
     * Generate 10-20 course titles for a given category using AI
     */
    public function generateCourseTitles(string $category, int $count = 15): array
    {
        try {
            $prompt = "Generate {$count} specific YouTube playlist search queries for educational content about: {$category}. 
                These should be real search terms that would find actual educational playlists on YouTube.
                Focus on complete courses, tutorials, and learning paths.
                Return only the search queries, one per line, without numbering or additional text.
                Examples: 'complete web development course', 'python for beginners tutorial', 'advanced javascript projects'";

            $response = Http::withOptions([
                'verify' => false, // Disable SSL verification for development
                'curl' => [
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                ]
            ])->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 500,
                'temperature' => 0.8,
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                $titles = array_filter(array_map('trim', explode("\n", $content)));
                
                return array_slice($titles, 0, $count);
            }

            Log::error('OpenAI API Error: ' . $response->body());
            return $this->getFallbackTitles($category);
            
        } catch (\Exception $e) {
            Log::error('AI Service Error: ' . $e->getMessage());
            return $this->getFallbackTitles($category);
        }
    }

    /**
     * Fallback method to generate basic titles if AI fails
     */
    private function getFallbackTitles(string $category): array
    {
        $templates = [
            "complete {$category} course",
            "{$category} tutorial for beginners",
            "advanced {$category} course",
            "{$category} full course",
            "learn {$category} from scratch",
            "professional {$category} training",
            "{$category} certification course",
            "{$category} masterclass",
            "{$category} online course",
            "{$category} complete guide",
            "introduction to {$category}",
            "{$category} fundamentals",
            "expert {$category} course",
            "{$category} bootcamp",
            "{$category} workshop"
        ];

        return $templates;
    }
}
