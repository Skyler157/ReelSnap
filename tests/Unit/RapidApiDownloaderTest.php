<?php

namespace Tests\Unit;

use App\Services\RapidApiDownloader;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RapidApiDownloaderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'reelsnap.rapidapi.base_url' => 'https://rapid.test',
            'reelsnap.rapidapi.key' => 'test-key',
            'reelsnap.rapidapi.host' => 'test-host',
            'reelsnap.security.allowed_video_hosts' => ['cdninstagram.com', 'fbcdn.net'],
        ]);
    }

    public function test_returns_error_when_response_shape_is_invalid(): void
    {
        Http::fake([
            'https://rapid.test/download*' => Http::response([
                'success' => true,
                'data' => ['unexpected' => []],
            ], 200),
        ]);

        $service = app(RapidApiDownloader::class);
        $result = $service->getVideoData('https://www.instagram.com/reel/abc123/');

        $this->assertFalse($result['success']);
        $this->assertSame('No video found in this post.', $result['message']);
    }

    public function test_rejects_video_urls_from_untrusted_hosts(): void
    {
        Http::fake([
            'https://rapid.test/download*' => Http::response([
                'success' => true,
                'data' => [
                    'medias' => [
                        [
                            'type' => 'video',
                            'url' => 'https://malicious.example/video.mp4',
                        ],
                    ],
                    'title' => 'x',
                    'author' => 'y',
                    'duration' => '1',
                ],
            ], 200),
        ]);

        $service = app(RapidApiDownloader::class);
        $result = $service->getVideoData('https://www.instagram.com/reel/abc123/');

        $this->assertFalse($result['success']);
        $this->assertSame('No video found in this post.', $result['message']);
    }

    public function test_returns_video_data_for_allowed_video_host(): void
    {
        Http::fake([
            'https://rapid.test/download*' => Http::response([
                'success' => true,
                'data' => [
                    'medias' => [
                        [
                            'type' => 'video',
                            'url' => 'https://video.cdninstagram.com/reel.mp4',
                        ],
                    ],
                    'thumbnail' => 'https://video.cdninstagram.com/thumb.jpg',
                    'title' => 'Test Reel',
                    'author' => 'creator',
                    'duration' => '22',
                ],
            ], 200),
        ]);

        $service = app(RapidApiDownloader::class);
        $result = $service->getVideoData('https://www.instagram.com/reel/abc123/');

        $this->assertTrue($result['success']);
        $this->assertSame('https://video.cdninstagram.com/reel.mp4', $result['video_url']);
        $this->assertSame('Test Reel', $result['title']);
    }
}
