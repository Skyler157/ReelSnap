# ReelSnap - Instagram Reel Downloader

ReelSnap is a Laravel-based web application that allows users to download Instagram Reels/Posts through RapidAPI, displays a preview, shows metadata, and provides a direct download option.


## RapidAPI Integration

The application integrates with a third-party Instagram downloader API through RapidAPI.  
RapidAPI acts as an API marketplace and gateway, handling authentication, rate limiting, and request routing to the external service provider.

This allows the system to securely fetch downloadable media URLs without directly managing external API infrastructure.


## Features

- Strict Instagram URL validation 
- Signed, time-limited download links
- Signed, time-limited preview page with close (`X`) back to home
- Streamed file downloads (reduced memory usage on large videos)
- Safe outbound host allowlist for video URLs
- Request throttling on download submission
- Privacy-aware analytics storage (IP stored as HMAC hash, not raw IP)

## Tech Stack

- Backend: Laravel 12
- Frontend: Blade templates + custom CSS
- HTTP Client: Laravel HTTP Client (`Illuminate\Support\Facades\Http`)
- API Provider: RapidAPI (Instagram downloader endpoint)
- Database: MySQL (or SQLite for testing)

## Configuration

Set these values in `.env`:

```env
APP_NAME=ReelSnap
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

RAPIDAPI_KEY=your_rapidapi_key_here
RAPIDAPI_HOST=instagram-reels-downloader-api.p.rapidapi.com
RAPIDAPI_BASE_URL=https://instagram-downloader-api.p.rapidapi.com

RAPIDAPI_CONNECT_TIMEOUT=5
RAPIDAPI_TIMEOUT=10
REEL_DOWNLOAD_TIMEOUT=120
RAPIDAPI_RETRIES=2
RAPIDAPI_RETRY_DELAY_MS=200

ALLOWED_VIDEO_HOSTS=cdninstagram.com,fbcdn.net
```

## Installation

1. Clone the repository:

```bash
git clone https://github.com/Skyler157/ReelSnap.git
cd ReelSnap
```

2. Install dependencies:

```bash
composer install
```

3. Create environment file and app key:

```bash
cp .env.example .env
php artisan key:generate
```

4. Update `.env` with your RapidAPI credentials and config values.

5. Run migrations:

```bash
php artisan migrate
```

6. Clear config/cache:

```bash
php artisan config:clear
php artisan cache:clear
```

7. Start the app:

```bash
php artisan serve
```

## Usage

1. Paste an Instagram Reel/Post URL on the home page.
2. Click `Download`.
3. After success:
   - Click `Download Video` for direct file download, or
   - Click `Open Preview` to watch on the internal preview page.



## Security and Privacy Notes

- Download and preview routes are signed and expire.
- Video source URLs are validated against an allowlist.
- Stored `ip_address` values are HMAC hashes for privacy.

