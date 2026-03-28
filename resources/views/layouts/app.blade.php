<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('messages.app_title'))</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @if(app()->getLocale() === 'ar')
    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @endif
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --bg-page: #f8f9fa;
            --bg-header: #1a1d29;
            --text-header: #ffffff;
            --text-header-muted: rgba(255, 255, 255, 0.7);
            --bg-card: #ffffff;
            --text-primary: #212529;
            --text-muted: #6c757d;
            --border-color: #e9ecef;
            --accent-red: #dc3545;
            --btn-primary-bg: #dc3545;
            --btn-primary-hover: #bb2d3b;
            --btn-danger-bg: #dc3545;
            --btn-danger-hover: #bb2d3b;
        }

        body {
            background: var(--bg-page);
            color: var(--text-primary);
        }

        .top-hero {
            background: var(--bg-header);
            color: var(--text-header);
        }

        .top-hero .brand {
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .surface-card {
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            background: var(--bg-card);
        }

        .muted-note {
            color: var(--text-header-muted);
        }

        .btn-pill {
            border-radius: 999px;
            padding-inline: 1.1rem;
        }

        .btn.btn-primary {
            background: var(--btn-primary-bg);
            border-color: var(--btn-primary-bg);
            color: #ffffff;
        }

        .btn.btn-primary:hover {
            background: var(--btn-primary-hover);
            border-color: var(--btn-primary-hover);
        }

        .btn.btn-primary:focus {
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
        }

        .btn.btn-danger {
            background: var(--btn-danger-bg);
            border-color: var(--btn-danger-bg);
            color: #ffffff;
        }

        .btn.btn-danger:hover {
            background: var(--btn-danger-hover);
            border-color: var(--btn-danger-hover);
        }

        .btn.btn-outline-danger {
            color: var(--btn-danger-bg);
            border-color: var(--btn-danger-bg);
        }

        .btn.btn-outline-danger:hover {
            background: var(--btn-danger-bg);
            border-color: var(--btn-danger-hover);
            color: #ffffff;
        }

        .badge.bg-danger {
            background: var(--btn-danger-bg) !important;
        }

        .badge.text-bg-danger {
            background: var(--btn-danger-bg) !important;
        }

        .badge.bg-primary {
            background: var(--btn-primary-bg) !important;
        }

        .badge.text-bg-primary {
            background: var(--btn-primary-bg) !important;
        }

        .text-primary {
            color: var(--btn-primary-bg) !important;
        }

        .btn-outline-primary {
            color: var(--btn-primary-bg);
            border-color: var(--btn-primary-bg);
        }

        .btn-outline-primary:hover {
            background: var(--btn-primary-bg);
            border-color: var(--btn-primary-hover);
            color: #ffffff;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <header class="top-hero">
        <div class="container py-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <a class="text-white text-decoration-none brand" href="{{ route('playlists.index') }}">
                    <i class="fab fa-youtube me-2"></i>
                    {{ __('messages.app_title') }}
                </a>
                
                <!-- Language Switcher -->
                <div class="d-flex align-items-center gap-3">
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-globe"></i>
                            {{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 {{ app()->getLocale() === 'en' ? 'active' : '' }}" 
                                   href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}">
                                    <span>🇺🇸</span> English
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 {{ app()->getLocale() === 'ar' ? 'active' : '' }}" 
                                   href="{{ request()->fullUrlWithQuery(['lang' => 'ar']) }}">
                                    <span>🇸🇦</span> العربية
                                </a>
                            </li>
                        </ul>
                    </div>
                    {{-- <span class="muted-note small">{{ __('messages.backend_test') }}</span> --}}
                </div>
            </div>

            @if(View::hasSection('hero_title'))
                <div class="text-center mt-4">
                    <div class="h4 mb-1 fw-bold">@yield('hero_title')</div>
                    @if(View::hasSection('hero_subtitle'))
                        <div class="muted-note small">@yield('hero_subtitle')</div>
                    @endif
                </div>
            @endif
        </div>
    </header>

    <main class="container my-4">
        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    @stack('scripts')
</body>
</html>
