
ReelSnap - Instagram Reel Downloader

ReelSnap is a lightweight Laravel-based web application that allows users to download Instagram Reels instantly. It fetches video data via a RapidAPI Instagram Reels endpoint, displays a preview, shows metadata, and provides a direct download option.

Features

Download Instagram Reels by URL

Video preview before downloading

Metadata display: title, author, duration

Direct download button

Download counter

API key-based authentication

Loading spinner during fetch

Proper error handling and URL validation

Rate-limiting to prevent abuse

Technologies Used

Backend: Laravel 12

Frontend: Blade + Tailwind CSS

HTTP Client: Laravel HTTP (Illuminate\Support\Facades\Http)

API Integration: RapidAPI Instagram Reels Downloader

Database: MySQL / SQLite / PostgreSQL (any Laravel-supported DB)

Installation

Clone the repository

git clone https://github.com/yourusername/ReelSnap.git
cd ReelSnap

Install dependencies

composer install

Set up environment variables

cp .env.example .env
php artisan key:generate

Edit .env with your RapidAPI key:

RAPIDAPI_KEY=your_rapidapi_key_here
RAPIDAPI_HOST=instagram-reels-downloader-api.p.rapidapi.com
RAPIDAPI_BASE_URL=https://instagram-downloader-api.p.rapidapi.com

Run migrations

php artisan migrate

Clear caches

php artisan config:clear
php artisan cache:clear

Start the server

php artisan serve



Usage

Paste an Instagram Reel URL into the input field.

Click the Download button.

Wait for the loading spinner to finish.

Preview the video, view metadata, and click Download Video to save locally.

Total downloads are tracked at the bottom of the page.