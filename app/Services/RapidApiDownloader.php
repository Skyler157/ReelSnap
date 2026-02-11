<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Services\Contracts\InstagramDownloaderInterface;

class RapidApiDownloader implements InstagramDownloaderInterface
{
    public function getVideoData(string $url): array
    {
        $response = Http::withHeaders([
            'X-RapidAPI-Key' => config('reelsnap.rapidapi.key'),
            'X-RapidAPI-Host' => config('reelsnap.rapidapi.host'),
        ])->get(config('reelsnap.rapidapi.base_url') . '/download', [
            'url' => $url
        ]);

        if (!$response->successful()) {
            return [
                'success' => false,
                'message' => 'API request failed',
                'error' => $response->json()
            ];
        }

        $data = $response->json();

        if (!isset($data['success']) || !$data['success']) {
            return [
                'success' => false,
                'message' => $data['message'] ?? 'Unknown API error'
            ];
        }

        $videoUrl = null;

        foreach ($data['data']['medias'] as $media) {
            if ($media['type'] === 'video') {
                $videoUrl = $media['url'];
                break;
            }
        }

        return [
            'success' => true,
            'video_url' => $videoUrl,
            'thumbnail' => $data['data']['thumbnail'] ?? null,
            'title' => $data['data']['title'] ?? 'Instagram Reel',
            'author' => $data['data']['author'] ?? null,
            'duration' => $data['data']['duration'] ?? null
        ];
    }
}