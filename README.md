# REELSNAP - Instagram Reel Downloader

**ReelSnap** is a lightweight Laravel-based web application that allows users to download Instagram Reels instantly. It fetches video data via a RapidAPI Instagram Reels endpoint, displays a preview, shows metadata, and provides a direct download option.


## Features

- Download Instagram Reels by URL  
- Video preview before downloading  
- Metadata display: title, author, duration  
- Direct download button  
- Download counter  
- API key-based authentication  
- Loading spinner during fetch  
- Proper error handling and URL validation  
- Rate-limiting to prevent abuse  


## Technologies Used

- **Backend:** Laravel 12  
- **Frontend:** Blade + Tailwind CSS  
- **HTTP Client:** Laravel HTTP (`Illuminate\Support\Facades\Http`)  
- **API Integration:** RapidAPI Instagram Reels Downloader  
- **Database:** MySQL 



## Installation

1. **Clone the repository**

```bash
git clone https://github.com/Skyler157/ReelSnap.git
cd ReelSnap

2. **Install dependencies**

composer install

3. **Set up environment variables**

cp .env.example .env
php artisan key:generate

Edit .env with your RapidAPI key:

RAPIDAPI_KEY=your_rapidapi_key_here
RAPIDAPI_HOST=instagram-reels-downloader-api.p.rapidapi.com
RAPIDAPI_BASE_URL=https://instagram-downloader-api.p.rapidapi.com

4. **Run migrations**

php artisan migrate

5. **Clear caches**

php artisan config:clear
php artisan cache:clear


php artisan serve 



USAGE

Paste an Instagram Reel URL into the input field.

Click the Download button.

Wait for the loading spinner to finish.

Preview the video, view metadata, and click Download Video to save locally.

