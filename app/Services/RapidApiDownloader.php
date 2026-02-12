<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\Contracts\InstagramDownloaderInterface;
use Throwable;

class RapidApiDownloader implements InstagramDownloaderInterface
{
    public function getVideoData(string $url): array
    {
        try {
            $response = Http::withHeaders([
                'X-RapidAPI-Key' => config('reelsnap.rapidapi.key'),
                'X-RapidAPI-Host' => config('reelsnap.rapidapi.host'),
            ])
            ->connectTimeout((int) config('reelsnap.http.connect_timeout', 5))
            ->timeout((int) config('reelsnap.http.timeout', 10))
            ->retry((int) config('reelsnap.http.retries', 2), (int) config('reelsnap.http.retry_delay_ms', 200))
            ->get(config('reelsnap.rapidapi.base_url') . '/download', [
                'url' => $url
            ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'API request failed.'
                ];
            }

            $data = $response->json();

            if (!is_array($data) || !($data['success'] ?? false)) {
                return [
                    'success' => false,
                    'message' => $data['message'] ?? 'Invalid response from API.'
                ];
            }

            $videoUrl = $this->extractVideoUrl($data);

            if (!$videoUrl) {
                return [
                    'success' => false,
                    'message' => 'No video found in this post.'
                ];
            }

            return [
                'success' => true,
                'video_url' => $videoUrl,
                'thumbnail' => $data['data']['thumbnail'] ?? null,
                'title' => $data['data']['title'] ?? 'Instagram Reel',
                'author' => $data['data']['author'] ?? 'Unknown',
                'duration' => $data['data']['duration'] ?? 'N/A'
            ];

        } catch (Throwable $e) {
            Log::error('RapidAPI downloader failed.', [
                'message' => $e->getMessage(),
                'url' => $url,
            ]);

            return [
                'success' => false,
                'message' => 'Server error occurred.'
            ];
        }
    }

    private function extractVideoUrl(array $data): ?string
    {
        $medias = data_get($data, 'data.medias');

        if (!is_array($medias)) {
            return null;
        }

        foreach ($medias as $media) {
            if (!is_array($media) || ($media['type'] ?? null) !== 'video') {
                continue;
            }

            $candidateUrl = (string) ($media['url'] ?? '');

            if ($this->isSafeVideoUrl($candidateUrl)) {
                return $candidateUrl;
            }
        }

        return null;
    }

    private function isSafeVideoUrl(string $url): bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host = strtolower($parts['host'] ?? '');

        if ($scheme !== 'https' || $host === '') {
            return false;
        }

        $allowedHosts = config('reelsnap.security.allowed_video_hosts', []);

        foreach ($allowedHosts as $allowedHost) {
            if ($host === $allowedHost || str_ends_with($host, '.' . $allowedHost)) {
                return true;
            }
        }

        return false;
    }
}
