<?php

namespace App\Http\Controllers;

use App\Services\PlaylistScrapingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PlaylistController extends Controller
{
    private PlaylistScrapingService $scrapingService;

    public function __construct(PlaylistScrapingService $scrapingService)
    {
        $this->scrapingService = $scrapingService;
    }

    /**
     * Display the main page
     */
    public function index(Request $request)
    {
        $categories = $this->scrapingService->getCategories();
        $categorySlug = $request->query('category');
        $page = (int) $request->query('page', 1);

        if (!empty($categorySlug)) {
            $playlists = $this->scrapingService->getPlaylistsByCategory($categorySlug, $page, 8);
        } else {
            $playlists = $this->scrapingService->getPlaylists($page, 8);
        }
        
        return view('playlists.index', [
            'categories' => $categories,
            'playlists' => $playlists,
            'currentPage' => $page,
            'selectedCategory' => $categorySlug,
        ]);
    }

    /**
     * Start scraping playlists for categories
     */
    public function scrape(Request $request): JsonResponse
    {
        $categories = $request->input('categories', '');
        
        if (empty($categories)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide at least one category'
            ]);
        }

        $categoryArray = array_filter(array_map('trim', explode("\n", $categories)));
        
        if (empty($categoryArray)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide valid categories'
            ]);
        }

        try {
            $results = $this->scrapingService->scrapeCategories($categoryArray);
            return response()->json([
                'success' => true,
                'results' => $results,
                'message' => 'Scraping completed successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error during scraping: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get playlists with pagination
     */
    public function getPlaylists(Request $request): JsonResponse
    {
        $page = $request->input('page', 1);
        $category = $request->input('category', null);
        
        try {
            if ($category) {
                $playlists = $this->scrapingService->getPlaylistsByCategory($category, $page);
            } else {
                $playlists = $this->scrapingService->getPlaylists($page);
            }
            
            return response()->json([
                'success' => true,
                'playlists' => [
                    'data' => $playlists->items(),
                    'current_page' => $playlists->currentPage(),
                    'last_page' => $playlists->lastPage(),
                    'per_page' => $playlists->perPage(),
                    'total' => $playlists->total(),
                    'links' => $playlists->links()->toHtml()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching playlists: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get categories
     */
    public function getCategories(): JsonResponse
    {
        try {
            $categories = $this->scrapingService->getCategories();
            
            return response()->json([
                'success' => true,
                'categories' => $categories
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching categories: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Test API connections
     */
    public function test(): JsonResponse
    {
        $results = [];
        
        // Test YouTube API
        try {
            $youtubeService = app(\App\Services\YouTubeService::class);
            $testResults = $youtubeService->searchPlaylists('Laravel tutorial', 2);
            $results['youtube'] = [
                'success' => true,
                'count' => count($testResults),
                'sample' => $testResults[0] ?? null
            ];
        } catch (\Exception $e) {
            $results['youtube'] = [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
        
        // Test OpenAI API
        try {
            $aiService = app(\App\Services\AIService::class);
            $testTitles = $aiService->generateCourseTitles('Programming', 3);
            $results['openai'] = [
                'success' => true,
                'count' => count($testTitles),
                'sample' => $testTitles
            ];
        } catch (\Exception $e) {
            $results['openai'] = [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
        
        return response()->json($results);
    }

    /**
     * Simple test with common search term
     */
    public function simpleTest(): JsonResponse
    {
        try {
            $youtubeService = app(\App\Services\YouTubeService::class);
            $testResults = $youtubeService->searchPlaylists('javascript tutorial', 5);
            
            return response()->json([
                'success' => true,
                'query' => 'javascript tutorial',
                'count' => count($testResults),
                'results' => $testResults
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Basic YouTube API test
     */
    public function basicTest(): JsonResponse
    {
        try {
            $apiKey = config('services.youtube.api_key');
            
            // Test basic API connection without complex parameters
            $client = new \Google\Client();
            $client->setDeveloperKey($apiKey);
            $youtube = new \Google\Service\YouTube($client);
            
            // Try a very simple search
            $response = $youtube->search->listSearch('snippet', [
                'q' => 'music',
                'type' => 'video',
                'maxResults' => 5
            ]);
            
            return response()->json([
                'success' => true,
                'api_key_configured' => !empty($apiKey),
                'api_key_length' => strlen($apiKey),
                'total_results' => $response->getPageInfo()->getTotalResults(),
                'items_count' => count($response->getItems()),
                'sample_item' => !empty($response->getItems()) ? [
                    'title' => $response->getItems()[0]->getSnippet()->getTitle(),
                    'type' => $response->getItems()[0]->getId()->getKind()
                ] : null
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'error_details' => $e->getTraceAsString()
            ]);
        }
    }
}
