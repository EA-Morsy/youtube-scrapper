<?php

namespace App\Services;

use Google\Client;
use Google\Service\YouTube;
use Illuminate\Support\Facades\Log;

class YouTubeService
{
    private YouTube $youtube;

    public function __construct()
    {
        $client = new Client();
        $client->setDeveloperKey(config('services.youtube.api_key'));
        
        // Fix SSL certificate issue for development
        $httpClient = new \GuzzleHttp\Client([
            'verify' => false, // Disable SSL verification for development
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ]
        ]);
        $client->setHttpClient($httpClient);
        
        $this->youtube = new YouTube($client);
    }

    /**
     * Search for playlists based on a query
     */
    public function searchPlaylists(string $query, int $maxResults = 50): array
    {
        try {
            Log::info("Searching YouTube for: " . $query);
            
            $response = $this->youtube->search->listSearch('snippet', [
                'q' => $query,
                'type' => 'playlist',
                'maxResults' => $maxResults,
                'part' => 'snippet',
                'relevanceLanguage' => 'en',
                'order' => 'relevance'
            ]);

            $items = $response->getItems();
            Log::info("YouTube search returned " . count($items) . " items for query: " . $query);

            $playlists = [];
            foreach ($items as $index => $item) {
                try {
                    $playlistId = $item->getId()->getPlaylistId();
                    $snippet = $item->getSnippet();
                    
                    Log::info("Processing item {$index}: Playlist ID = " . $playlistId);

                    // Get detailed playlist information
                    $playlistDetails = $this->youtube->playlists->listPlaylists('snippet,contentDetails', [
                        'id' => $playlistId
                    ]);

                    if (empty($playlistDetails->getItems())) {
                        Log::warning("No playlist details found for ID: " . $playlistId);
                        continue;
                    }

                    $playlist = $playlistDetails->getItems()[0];
                    $contentDetails = $playlist->getContentDetails();

                    $playlists[] = [
                        'playlist_id' => $playlistId,
                        'title' => $snippet->getTitle(),
                        'description' => $snippet->getDescription(),
                        'thumbnail' => $snippet->getThumbnails()->getHigh() ? $snippet->getThumbnails()->getHigh()->getUrl() : null,
                        'channel_name' => $snippet->getChannelTitle(),
                        'video_count' => (int) $contentDetails->getItemCount(),
                        'duration' => null // YouTube doesn't provide total playlist duration via API
                    ];
                    
                    Log::info("Successfully processed playlist: " . $snippet->getTitle());
                } catch (\Exception $e) {
                    Log::error("Error processing playlist item: " . $e->getMessage());
                    continue;
                }
            }

            Log::info("Returning " . count($playlists) . " playlists for query: " . $query);
            return $playlists;
        } catch (\Exception $e) {
            Log::error('YouTube API Error for query "' . $query . '": ' . $e->getMessage());
            return [];
        }
    }
}
