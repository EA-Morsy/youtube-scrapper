# YouTube Playlist Scraper

A Laravel-based web application that discovers educational YouTube playlists using AI-generated search queries and displays them in a modern Bootstrap UI with multilingual support.

## Features

- **AI-Powered Search**: Uses OpenAI API to generate 10-20 educational course titles per category
- **YouTube Integration**: Searches YouTube Data API for playlists based on AI-generated queries
- **Deduplication**: Prevents storing duplicate playlists based on YouTube playlist ID
- **Modern UI**: Responsive Bootstrap 5 interface with RTL/LTR support
- **Multilingual Support**: English and Arabic localization with automatic language switching
- **Real-time Progress**: Shows scraping progress with live updates
- **Category Management**: Organize playlists by categories with counts
- **Pagination**: Efficient display of playlists with pagination
- **Custom Design**: Red-themed UI matching provided design specifications

## Requirements

- PHP 8.2+
- Composer
- SQLite (default) or MySQL/PostgreSQL
- YouTube Data API v3 key
- OpenAI API key

## Installation

### 1. Clone and Install Dependencies

```bash
git clone <repository-url>
cd youtube-scrapper
composer install
```

### 2. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configure API Keys

Edit your `.env` file and add your API keys:

```env
# YouTube Data API
YOUTUBE_API_KEY=your_youtube_api_key_here

# OpenAI API
OPENAI_API_KEY=your_openai_api_key_here

# Optional: Set default locale
APP_LOCALE=en
```

#### Getting API Keys:

**YouTube Data API:**
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing one
3. Enable YouTube Data API v3
4. Create credentials (API Key)
5. Copy the API key to your `.env` file

**OpenAI API:**
1. Go to [OpenAI Platform](https://platform.openai.com/)
2. Sign up or log in
3. Navigate to API Keys section
4. Create new API key
5. Copy the API key to your `.env` file

### 4. Database Setup

```bash
# For SQLite (default)
php artisan migrate

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=youtube-scrapper
DB_USERNAME=root
DB_PASSWORD=

# Then run migrations
php artisan migrate
```

### 5. Start the Application

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Usage

### Basic Usage

1. **Select Language**: Use the language switcher in the header (English/العربية)
2. **Enter Categories**: In the textarea, enter categories one per line (e.g., Marketing, Programming, Graphic Design)
3. **Start Scraping**: Click "Start Fetching" / "بدء الجمع" button
4. **Monitor Progress**: Watch the real-time progress bar and log
5. **View Results**: Discovered playlists appear in the results section
6. **Filter by Category**: Click on categories in the sidebar to filter results

### Language Support

The application supports:
- **English**: Full LTR layout with English text
- **العربية**: Full RTL layout with Arabic text
- **Automatic Detection**: Language preference is saved in session
- **URL Parameters**: Use `?lang=en` or `?lang=ar` to switch languages

### What Happens Behind the Scenes

1. **AI Query Generation**: For each category, the AI generates 15 educational search queries
2. **YouTube Search**: Each query searches YouTube for playlists (max 10 results)
3. **Playlist Selection**: Takes first 2 playlists per query (30 playlists per category max)
4. **Deduplication**: Checks if playlist already exists using YouTube playlist ID
5. **Database Storage**: Saves unique playlists with metadata

### API Endpoints

- `GET /` - Main interface
- `GET /?lang=en` - English version
- `GET /?lang=ar` - Arabic version
- `POST /api/scrape` - Start scraping process
- `GET /api/playlists` - Get playlists with pagination
- `GET /api/categories` - Get categories with playlist counts

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── PlaylistController.php     # Main controller
│   └── Middleware/
│       └── SetLocale.php              # Language switching middleware
├── Models/
│   ├── Category.php                   # Category model
│   └── Playlist.php                   # Playlist model
└── Services/
    ├── AIService.php                  # OpenAI integration
    ├── YouTubeService.php              # YouTube API integration
    └── PlaylistScrapingService.php    # Main scraping logic

database/
├── migrations/
│   ├── 2026_03_27_231442_create_categories_table.php
│   └── 2026_03_27_231443_create_playlists_table.php
└── database.sqlite

resources/
├── lang/
│   ├── en/
│   │   └── messages.php               # English translations
│   └── ar/
│       └── messages.php               # Arabic translations
└── views/
    ├── layouts/
    │   └── app.blade.php              # Main layout with language switcher
    └── playlists/
        └── index.blade.php            # Main interface
```

## Database Schema

### Categories Table
- `id` - Primary key
- `name` - Category name (unique)
- `slug` - URL-friendly slug (unique)
- `created_at`, `updated_at` - Timestamps

### Playlists Table
- `id` - Primary key
- `playlist_id` - YouTube playlist ID (unique)
- `title` - Playlist title
- `description` - Playlist description
- `thumbnail` - Thumbnail URL
- `channel_name` - Channel name
- `category_id` - Foreign key to categories
- `video_count` - Number of videos
- `duration` - Duration (null, not provided by YouTube API)
- `created_at`, `updated_at` - Timestamps

## Features Implemented

✅ **AI Integration**: OpenAI API for generating search queries
✅ **YouTube Search**: YouTube Data API v3 integration
✅ **Database Design**: Proper migrations and relationships
✅ **Deduplication**: Prevents duplicate playlists
✅ **Modern UI**: Bootstrap 5 with RTL/LTR support
✅ **Multilingual Support**: English and Arabic localization
✅ **Real-time Updates**: AJAX-based progress tracking
✅ **Responsive Design**: Mobile-friendly interface
✅ **Pagination**: Efficient playlist display
✅ **Category Filtering**: Filter playlists by category
✅ **Custom Design**: Red-themed UI matching design specifications
✅ **Error Handling**: Comprehensive error management
✅ **Rate Limiting**: Prevents API abuse

## UI/UX Features

- **Language Switcher**: Dropdown in header with flag icons
- **Responsive Layout**: Works on desktop, tablet, and mobile
- **Dark Header**: Professional dark header with gradient effect
- **Red Theme**: Consistent red color scheme throughout
- **Smooth Animations**: Hover effects and transitions
- **RTL Support**: Automatic right-to-left layout for Arabic
- **Card-based Design**: Modern card layout for playlists
- **Progress Indicators**: Real-time scraping progress

## API Limits and Considerations

- **YouTube API**: 100 units per query, 10,000 units per day (free tier)
- **OpenAI API**: Rate limits apply based on plan
- **Rate Limiting**: Built-in delays to prevent API abuse
- **Deduplication**: Ensures no duplicate playlists stored

## Troubleshooting

### Common Issues

1. **API Key Errors**: Verify your API keys are correct and have proper permissions
2. **Database Errors**: Ensure database permissions are correct
3. **Migration Issues**: Clear cache: `php artisan config:clear`
4. **Memory Issues**: Increase PHP memory limit if needed
5. **Language Issues**: Clear cache after adding translations: `php artisan cache:clear`

### Logs

Check Laravel logs for detailed error information:
```bash
php artisan log:clear
# Then check storage/logs/laravel.log
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly (including both languages)
5. Submit a pull request

## License

This project is open-sourced software licensed under the MIT license.
