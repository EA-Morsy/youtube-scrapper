@extends('layouts.app')

@section('title', __('messages.app_title'))

@section('hero_title')
    {{ __('messages.app_title') }}
@endsection

@section('hero_subtitle')
    {{ __('messages.app_subtitle') }}
@endsection

@section('content')

<div class="row g-3">
    <!-- Input Section -->
    <div class="col-lg-12">
        <div class="card surface-card">
            <div class="card-body p-4">
            

                <form id="scrapeForm">
                    
                    <div class="row g-3">
                        <div class="col-md-8">
                                <div class="mb-3 ">
                    <p class="text-muted mb-0 fw-semibold"> {{ __('messages.input_categories') }} (   {{ __('messages.one_per_line') }})</p>
                     </div>
                            <textarea 
                                class="form-control textarea-custom" 
                                id="categories" 
                                name="categories" 
                                rows="6" 
                                placeholder="{{ __('messages.search_placeholder') }}"
                                required
                            ></textarea>
                        </div>
                        <div class="col-md-4 d-flex flex-column justify-content-end gap-2">
                            <button type="submit" class="btn btn-danger btn-lg btn-pill" id="startBtn">
                               {{ __('messages.start_fetching') }} <i class="fas fa-play ms-2"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-lg btn-pill" id="stopBtn" disabled>
                                {{ __('messages.stop') }} <i class="fas fa-stop ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <div id="progressSection" class="mt-4" style="display: none;">
                    <div class="card surface-card-soft">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="fw-semibold">{{ __('messages.fetching_courses') }}</div>
                                <div class="small text-muted" id="progressHint"></div>
                            </div>
                            <div class="progress mb-3" style="height: 10px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                     role="progressbar" 
                                     style="width: 0%" 
                                     id="progressBar">
                                    0%
                                </div>
                            </div>
                            <div id="progressLog" class="small text-muted" style="max-height: 200px; overflow-y: auto;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   
</div>



<!-- Discovered Courses Section -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card surface-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div class="fw-semibold">
                        <i class="fas fa-graduation-cap me-2 text-primary"></i>
                        {{ __('messages.discovered_courses') }}
                    </div>
                    @if(!empty($selectedCategory ?? null))
                        <div class="small text-muted">{{ __('messages.results_for') }}: <span class="fw-semibold">{{ $selectedCategory }}</span></div>
                    @else
                        <div class="small text-muted">{{ __('messages.all_categories') }}</div>
                    @endif
                </div>
<!-- Categories Section -->

            <div class="card-body p-4">
                <div class="mb-4">
                    {{-- <h5 class="fw-bold mb-2">{{ __('messages.discovered_courses') }}</h5> --}}
                    <p class="text-muted mb-0">
                        {{ __('messages.courses_found_in_categories', ['count' => $playlists->total(), 'categories' => count($categories)]) }}
                    </p>
                </div>

                <!-- Horizontal Categories Pills -->
                <div class="categories-pills-container mb-4">
                    <div class="categories-pills">
                        <a 
                            href="{{ route('playlists.index') }}"
                            class="category-pill {{ empty($selectedCategory ?? null) ? 'active' : '' }}"
                        >
                            {{ __('messages.all_categories') }} ({{ $playlists->total() }})
                        </a>

                        @forelse($categories as $category)
                            <a 
                                href="{{ route('playlists.index', ['category' => $category['slug']]) }}"
                                class="category-pill {{ (($selectedCategory ?? null) === $category['slug']) ? 'active' : '' }}"
                            >
                                {{ $category['name'] }} ({{ $category['playlists_count'] }})
                            </a>
                        @empty
                        @endforelse
                    </div>
                </div>
            </div>

                <div id="playlistsContainer">
                    <div class="row g-3 mt-4">
                        @forelse($playlists as $playlist)
                            <div class="col-md-6 col-lg-3 d-flex">
                                <div class="card surface-card playlist-card flex-fill h-100">
                                
                                    <img src="{{ $playlist->thumbnail ?? asset('images/default-playlist.jpg') }}" 
                                    
                                         class="card-img-top" 
                                         alt="{{ $playlist->title }}"
                                         style="height: 180px; object-fit: cover;">
                                    <div class="card-body d-flex flex-column">
                                      
                                        <h6 class="card-title fw-semibold mb-2">{{ Str::limit($playlist->title, 50) }}</h6>
                                        <p class="card-text small text-muted mb-3 flex-grow-1">{{ Str::limit($playlist->description, 80) }}</p>
                                         <div class="small text-muted" style="min-height: 20px; display: flex; align-items: center;">
                                                <i class="fas fa-user me-1"></i> {{ $playlist->channel_name }}
                                            </div>
                                        <div class="d-flex justify-content-between mb-3" style="min-height: 40px;">
                                           
                                            <div class="d-flex gap-2 align-items-center" style="display: flex; align-items: center;">
                                                <span class="badge bg-danger small">
                                                    {{ $playlist->video_count ?? 0 }} {{ __('messages.videos') }}
                                                </span>
                                                <span class="badge bg-secondary category-badge">
                                                    {{ $playlist->category?->name ?? '-' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mt-auto">
                                            <a href="https://youtube.com/playlist?list={{ $playlist->playlist_id }}" 
                                               target="_blank"
                                               class="btn btn-primary btn-sm btn-pill w-100">
                                                <i class="fab fa-youtube me-1"></i>
                                                {{ __('messages.view_playlist') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted">
                                <i class="fas fa-search fa-3x mb-3"></i>
                                <h5>{{ __('messages.no_results') }}</h5>
                                <p>{{ __('messages.categories') }} {{ __('messages.filter_results') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Pagination -->
                @if($playlists->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        <nav aria-label="Playlist pagination">
                            {{ $playlists->appends(['category' => $selectedCategory ?? null])->links('pagination.custom') }}
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .input-surface {
        border-radius: 12px;
        border: 1px solid var(--border-color);
        background: var(--bg-card);
        padding: 14px 14px;
        resize: vertical;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .input-surface:focus {
        border-color: var(--accent-blue);
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.12);
        outline: 0;
    }

    .textarea-custom {
        border-radius: 12px;
        border: 2px solid #e9ecef;
        background: #ffffff;
        padding: 12px 15px;
        resize: vertical;
        transition: all 0.2s ease;
        font-size: 15px;
        line-height: 1.6;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        text-align: right;
        direction: rtl;
    }

    .textarea-custom:focus {
        border-color: var(--accent-red);
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.15);
        outline: 0;
        transform: translateY(-1px);
    }

    .textarea-custom::placeholder {
        color: #adb5bd;
        font-style: normal;
        text-align: right;
        direction: rtl;
    }

    .surface-card-soft {
        border: 1px solid var(--border-color);
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        background: var(--bg-card);
        overflow: hidden;
    }

    .category-list {
        display: grid;
        gap: 10px;
        max-height: min(230px, calc(100vh - 260px));
        overflow-y: auto;
        padding-inline-end: 6px;
    }

    .category-list::-webkit-scrollbar {
        width: 8px;
    }

    .category-list::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 999px;
    }

    .category-list::-webkit-scrollbar-track {
        background: transparent;
    }

    .category-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-decoration: none;
        padding: 12px 14px;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        background: var(--bg-card);
        color: var(--text-primary);
        transition: all 0.12s ease;
    }

    .category-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        border-color: var(--accent-blue);
        color: var(--text-primary);
    }

    .category-item.active {
        border-color: var(--accent-blue);
        background: rgba(13, 110, 253, 0.08);
    }

    .playlist-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .playlist-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    .playlist-card img {
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
    }

    /* Pagination styling */
    .pagination {
        justify-content: center;
        margin-top: 2rem;
    }

    .pagination .page-link {
        color: var(--accent-red);
        border: 1px solid var(--border-color);
        margin: 0 2px;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .pagination .page-link:hover {
        background: var(--accent-red);
        color: white;
        border-color: var(--accent-red);
        transform: translateY(-1px);
    }

    .pagination .page-item.active .page-link {
        background: var(--accent-red);
        border-color: var(--accent-red);
        color: white;
    }

    .pagination .page-item.disabled .page-link {
        color: var(--text-muted);
        background-color: var(--bg-page);
        border-color: var(--border-color);
    }

    .pagination .page-link:focus {
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
    }

    /* RTL Support */
   [dir="rtl"] .pagination {
        flex-direction: row-reverse;
    }

    /* Categories Pills Styles */
    .categories-pills-container {
        overflow-x: auto;
        padding-bottom: 8px;
    }

    .categories-pills-container::-webkit-scrollbar {
        height: 6px;
    }

    .categories-pills-container::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 999px;
    }

    .categories-pills-container::-webkit-scrollbar-track {
        background: transparent;
    }

    .categories-pills {
        display: flex;
        gap: 12px;
        white-space: nowrap;
        padding: 4px;
    }

    .category-pill {
        display: inline-flex;
        align-items: center;
        padding: 10px 20px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
        background: #f8f9fa;
        color: #6c757d;
        border: 1px solid #e9ecef;
        transition: all 0.2s ease;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .category-pill:hover {
        background: #e9ecef;
        color: #495057;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .category-pill.active {
        background: var(--accent-red);
        color: white;
        border-color: var(--accent-red);
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
    }

    .category-pill.active:hover {
        background: #bb2d3b;
        border-color: #bb2d3b;
        color: white;
    }

    .category-badge {
        font-size: 0.7rem;
        font-weight: 500;
        padding: 0.25rem 0.5rem;
        border-radius: 8px;
        background: linear-gradient(135deg, #6c757d, #5a6268) !important;
        border: none;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        min-width: 60px;
        text-align: center;
        white-space: nowrap;
    }

    .playlist-card .badge.bg-primary {
        font-size: 0.7rem;
        font-weight: 500;
        padding: 0.25rem 0.5rem;
        border-radius: 8px;
        background: linear-gradient(135deg, #0d6efd, #0056b3) !important;
        border: none;
        min-width: 60px;
        text-align: center;
        white-space: nowrap;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let isScraping = false;
    
    $('#scrapeForm').on('submit', function(e) {
        e.preventDefault();
        
        if (isScraping) return;
        
        const categories = $('#categories').val().trim();
        if (!categories) {
            alert('الرجاء إدخال فئة واحدة على الأقل');
            return;
        }
        
        startScraping(categories);
    });
    
    $('#stopBtn').on('click', function() {
        stopScraping();
    });
    
    function startScraping(categories) {
        isScraping = true;
        $('#startBtn').prop('disabled', true);
        $('#stopBtn').prop('disabled', false);
        $('#categories').prop('disabled', true);
        $('#progressSection').show();
        $('#progressLog').html('');
        updateProgress(0, 'بدء عملية الجمع...');
        
        $.ajax({
            url: '/api/scrape',
            method: 'POST',
            data: { categories: categories },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    handleScrapingResults(response.results);
                } else {
                    showError(response.message);
                }
            },
            error: function(xhr) {
                showError('حدث خطأ أثناء الجمع: ' + xhr.responseText);
            },
            complete: function() {
                stopScraping();
                refreshPlaylists();
                refreshCategories();
            }
        });
    }
    
    function stopScraping() {
        isScraping = false;
        $('#startBtn').prop('disabled', false);
        $('#stopBtn').prop('disabled', true);
        $('#categories').prop('disabled', false);
        updateProgress(100, 'اكتملت عملية الجمع');
    }
    
    function updateProgress(percentage, message) {
        $('#progressBar').css('width', percentage + '%').text(percentage + '%');
        if (message) {
            $('#progressLog').append('<div>' + message + '</div>');
            $('#progressLog').scrollTop($('#progressLog')[0].scrollHeight);
        }
    }
    
    function handleScrapingResults(results) {
        let totalPlaylists = 0;
        for (const [category, result] of Object.entries(results)) {
            if (result.error) {
                updateProgress(0, 'خطأ في فئة ' + category + ': ' + result.error);
            } else {
                totalPlaylists += result.playlists_saved;
                updateProgress(0, 'تم حفظ ' + result.playlists_saved + ' دورة في فئة ' + category);
            }
        }
        updateProgress(100, 'اكتملت عملية الجمع. تم حفظ ' + totalPlaylists + ' دورة إجمالية');
    }
    
    function showError(message) {
        $('#progressLog').append('<div class="text-danger">خطأ: ' + message + '</div>');
    }
    
    function refreshPlaylists() {
        $.get('/api/playlists', function(response) {
            if (response.success) {
                // Update playlists grid
                location.reload(); // Simple refresh for now
            }
        });
    }
    
    function refreshCategories() {
        $.get('/api/categories', function(response) {
            if (response.success) {
                // Update categories list
                location.reload(); // Simple refresh for now
            }
        });
    }
});
</script>
@endpush
