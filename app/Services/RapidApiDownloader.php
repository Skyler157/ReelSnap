<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Services\Contracts\InstagramDownloaderInterface;

class RapidApiDownloader implements InstagramDownloaderInterface
{
    public function getVideoData(string $url): array
    {
        try {
            $response = Http::withHeaders([
                'X-RapidAPI-Key' => config('reelsnap.rapidapi.key'),
                'X-RapidAPI-Host' => config('reelsnap.rapidapi.host'),
            ])->get(config('reelsnap.rapidapi.base_url') . '/download', [
                'url' => $url
            ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'API request failed.'
                ];
            }

            $data = $response->json();

            if (!isset($data['success']) || !$data['success']) {
                return [
                    'success' => false,
                    'message' => $data['message'] ?? 'Invalid response from API.'
                ];
            }

            $videoUrl = null;

            foreach ($data['data']['medias'] as $media) {
                if ($media['type'] === 'video') {
                    $videoUrl = $media['url'];
                    break;
                }
            }

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

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Server error occurred.'
            ];
        }
    }
}