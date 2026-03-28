@extends('layouts.app')

@section('title', 'Debug - YouTube Scraper')

@section('content')
<div class="container">
    <h1>Debug Information</h1>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>API Configuration</h5>
                </div>
                <div class="card-body">
                    <p><strong>YouTube API Key:</strong> 
                        @if(config('services.youtube.api_key'))
                            <span class="text-success">✓ Configured</span>
                        @else
                            <span class="text-danger">✗ Not configured</span>
                        @endif
                    </p>
                    <p><strong>OpenAI API Key:</strong> 
                        @if(config('services.openai.api_key'))
                            <span class="text-success">✓ Configured</span>
                        @else
                            <span class="text-danger">✗ Not configured</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Database Status</h5>
                </div>
                <div class="card-body">
                    <p><strong>Categories:</strong> {{ App\Models\Category::count() }}</p>
                    <p><strong>Playlists:</strong> {{ App\Models\Playlist::count() }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Test Scraping</h5>
                </div>
                <div class="card-body">
                    <form id="testForm">
                        <div class="mb-3">
                            <label for="testCategory" class="form-label">Test Category</label>
                            <input type="text" class="form-control" id="testCategory" value="Programming" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Test Scraping</button>
                    </form>
                    
                    <div id="testResults" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>API Connection Test</h5>
                </div>
                <div class="card-body">
                    <button id="testApiBtn" class="btn btn-info">Test API Connections</button>
                    <div id="apiResults" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#testForm').on('submit', function(e) {
        e.preventDefault();
        
        const category = $('#testCategory').val();
        $('#testResults').html('<div class="alert alert-info">Testing scraping for: ' + category + '</div>');
        
        $.ajax({
            url: '/api/scrape',
            method: 'POST',
            data: { categories: category },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    let html = '<div class="alert alert-success"><h6>Success!</h6>';
                    for (const [cat, result] of Object.entries(response.results)) {
                        html += '<p><strong>' + cat + ':</strong> ' + result.playlists_saved + ' playlists saved</p>';
                        if (result.saved_playlist_titles && result.saved_playlist_titles.length > 0) {
                            html += '<ul>';
                            result.saved_playlist_titles.forEach(title => {
                                html += '<li>' + title + '</li>';
                            });
                            html += '</ul>';
                        }
                    }
                    html += '</div>';
                    $('#testResults').html(html);
                } else {
                    $('#testResults').html('<div class="alert alert-danger">Error: ' + response.message + '</div>');
                }
            },
            error: function(xhr) {
                $('#testResults').html('<div class="alert alert-danger">AJAX Error: ' + xhr.responseText + '</div>');
            }
        });
    });
    
    $('#testApiBtn').on('click', function() {
        $('#apiResults').html('<div class="alert alert-info">Testing API connections...</div>');
        
        $.get('/api/test', function(response) {
            let html = '<div class="row">';
            
            // YouTube API Results
            html += '<div class="col-md-6">';
            html += '<div class="card">';
            html += '<div class="card-header">YouTube API</div>';
            html += '<div class="card-body">';
            if (response.youtube && response.youtube.success) {
                html += '<p class="text-success">✓ Connected</p>';
                html += '<p>Found ' + response.youtube.count + ' playlists</p>';
                if (response.youtube.sample) {
                    html += '<p><small>Sample: ' + response.youtube.sample.title + '</small></p>';
                }
            } else {
                html += '<p class="text-danger">✗ Failed</p>';
                html += '<p><small>Error: ' + (response.youtube ? response.youtube.error : 'Unknown') + '</small></p>';
            }
            html += '</div></div></div>';
            
            // OpenAI API Results
            html += '<div class="col-md-6">';
            html += '<div class="card">';
            html += '<div class="card-header">OpenAI API</div>';
            html += '<div class="card-body">';
            if (response.openai && response.openai.success) {
                html += '<p class="text-success">✓ Connected</p>';
                html += '<p>Generated ' + response.openai.count + ' titles</p>';
                if (response.openai.sample && response.openai.sample.length > 0) {
                    html += '<p><small>Sample: ' + response.openai.sample[0] + '</small></p>';
                }
            } else {
                html += '<p class="text-danger">✗ Failed</p>';
                html += '<p><small>Error: ' + (response.openai ? response.openai.error : 'Unknown') + '</small></p>';
            }
            html += '</div></div></div>';
            
            html += '</div>';
            $('#apiResults').html(html);
        }).fail(function(xhr) {
            $('#apiResults').html('<div class="alert alert-danger">API Test Failed: ' + xhr.responseText + '</div>');
        });
    });
});
</script>
@endpush
