<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Playlist;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PlaylistScrapingService
{
    private AIService $aiService;
    private YouTubeService $youtubeService;

    public function __construct(AIService $aiService, YouTubeService $youtubeService)
    {
        $this->aiService = $aiService;
        $this->youtubeService = $youtubeService;
    }

    /**
     * Scrape playlists for multiple categories
     */
    public function scrapeCategories(array $categories): array
    {
        $results = [];
        
        foreach ($categories as $categoryName) {
            $categoryName = trim($categoryName);
            if (empty($categoryName)) continue;
            
            $results[$categoryName] = $this->scrapeCategory($categoryName);
        }
        
        return $results;
    }

    /**
     * Scrape playlists for a single category
     */
    public function scrapeCategory(string $categoryName): array
    {
        try {
            // Create or get category
            $category = $this->getOrCreateCategory($categoryName);
            
            // Generate search queries using AI
            $searchQueries = $this->aiService->generateCourseTitles($categoryName, 15);
            
            $savedPlaylists = [];
            $totalProcessed = 0;
            
            // For each query, search YouTube and save playlists
            foreach ($searchQueries as $query) {
                $playlists = $this->youtubeService->searchPlaylists($query, 10);
                
                // Take first 2 playlists per query as per requirements
                $playlistsToSave = array_slice($playlists, 0, 2);
                
                foreach ($playlistsToSave as $playlistData) {
                    if ($this->savePlaylist($playlistData, $category->id)) {
                        $savedPlaylists[] = $playlistData['title'];
                    }
                }
                
                $totalProcessed += count($playlistsToSave);
                
                // Rate limiting to avoid API limits
                if ($totalProcessed % 10 === 0) {
                    sleep(1);
                }
            }
            
            return [
                'category' => $categoryName,
                'queries_processed' => count($searchQueries),
                'playlists_saved' => count($savedPlaylists),
                'saved_playlist_titles' => $savedPlaylists
            ];
            
        } catch (\Exception $e) {
            Log::error("Error scraping category {$categoryName}: " . $e->getMessage());
            return [
                'category' => $categoryName,
                'error' => $e->getMessage(),
                'queries_processed' => 0,
                'playlists_saved' => 0
            ];
        }
    }

    /**
     * Get or create a category
     */
    private function getOrCreateCategory(string $categoryName): Category
    {
        $slug = Str::slug($categoryName);
        
        return Category::firstOrCreate(
            ['slug' => $slug],
            ['name' => $categoryName]
        );
    }

    /**
     * Save playlist to database with deduplication
     */
    private function savePlaylist(array $playlistData, int $categoryId): bool
    {
        try {
            // Check if playlist already exists by playlist_id (deduplication)
            $existingPlaylist = Playlist::where('playlist_id', $playlistData['playlist_id'])->first();
            
            if ($existingPlaylist) {
                Log::info("Playlist already exists: {$playlistData['playlist_id']}");
                return false;
            }
            
            // Create new playlist
            Playlist::create([
                'playlist_id' => $playlistData['playlist_id'],
                'title' => $playlistData['title'],
                'description' => $playlistData['description'],
                'thumbnail' => $playlistData['thumbnail'],
                'channel_name' => $playlistData['channel_name'],
                'category_id' => $categoryId,
                'video_count' => $playlistData['video_count'],
                'duration' => $playlistData['duration'],
            ]);
            
            Log::info("Saved playlist: {$playlistData['title']}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Error saving playlist: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all playlists with pagination
     */
    public function getPlaylists(int $page = 1, int $perPage = 12)
    {
        return Playlist::with('category')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get playlists by category
     */
    public function getPlaylistsByCategory(string $categorySlug, int $page = 1, int $perPage = 12)
    {
        $category = Category::where('slug', $categorySlug)->firstOrFail();
        
        return $category->playlists()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get all categories
     */
    public function getCategories(): array
    {
        return Category::withCount('playlists')
            ->orderBy('name')
            ->get()
            ->toArray();
    }
}
