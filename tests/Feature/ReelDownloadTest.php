<?php

namespace Tests\Feature;

use App\Models\Download;
use App\Services\Contracts\InstagramDownloaderInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Mockery\MockInterface;
use Tests\TestCase;

class ReelDownloadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.key' => 'base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=',
            'reelsnap.security.allowed_video_hosts' => ['cdninstagram.com', 'fbcdn.net'],
        ]);
    }

    public function test_rejects_non_instagram_urls(): void
    {
        $response = $this->from('/')->post('/download', [
            'url' => 'https://evil.example/instagram.com/reel/abc123/',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors('url');
        $this->assertDatabaseCount('downloads', 0);
    }

    public function test_returns_error_when_downloader_fails(): void
    {
        $this->mock(InstagramDownloaderInterface::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getVideoData')
                ->once()
                ->andReturn([
                    'success' => false,
                    'message' => 'API request failed.',
                ]);
        });

        $response = $this->from('/')->post('/download', [
            'url' => 'https://www.instagram.com/reel/abc123/',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('errors');
        $this->assertDatabaseCount('downloads', 0);
    }

    public function test_persists_normalized_url_and_hashed_ip_on_success(): void
    {
        $this->mock(InstagramDownloaderInterface::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getVideoData')
                ->once()
                ->andReturn([
                    'success' => true,
                    'video_url' => 'https://scontent.cdninstagram.com/v/t50.2886.mp4',
                    'thumbnail' => null,
                    'title' => 'Sample',
                    'author' => 'Author',
                    'duration' => '10',
                ]);
        });

        $ipAddress = '10.20.30.40';
        $url = 'https://www.instagram.com/reel/abc123/?utm_source=test';

        $response = $this->withServerVariables(['REMOTE_ADDR' => $ipAddress])
            ->from('/')
            ->post('/download', ['url' => $url]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'Video fetched successfully!');

        $this->assertDatabaseHas('downloads', [
            'url' => 'https://www.instagram.com/reel/abc123/',
            'ip_address' => hash_hmac('sha256', $ipAddress, config('app.key')),
        ]);

        $this->assertSame(1, Download::count());
    }

    public function test_applies_throttle_limit_after_ten_requests_per_minute(): void
    {
        $this->mock(InstagramDownloaderInterface::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getVideoData')
                ->times(10)
                ->andReturn([
                    'success' => true,
                    'video_url' => 'https://scontent.cdninstagram.com/v/t50.2886.mp4',
                    'thumbnail' => null,
                    'title' => 'Sample',
                    'author' => 'Author',
                    'duration' => '10',
                ]);
        });

        for ($i = 0; $i < 10; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])
                ->from('/')
                ->post('/download', [
                    'url' => 'https://www.instagram.com/reel/abc123/',
                ])
                ->assertRedirect('/');
        }

        $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])
            ->post('/download', [
                'url' => 'https://www.instagram.com/reel/abc123/',
            ])
            ->assertStatus(429);
    }

    public function test_download_file_requires_valid_signature(): void
    {
        $this->get(route('download.file', [
            'video_url' => 'https://video.cdninstagram.com/reel.mp4',
            'title' => 'Test Reel',
        ]))->assertStatus(403);
    }

    public function test_download_file_returns_attachment_for_signed_request(): void
    {
        Http::fake([
            'https://video.cdninstagram.com/*' => Http::response('binary-video-content', 200, [
                'Content-Type' => 'video/mp4',
            ]),
        ]);

        $signedUrl = URL::temporarySignedRoute('download.file', now()->addMinutes(5), [
            'video_url' => 'https://video.cdninstagram.com/reel.mp4',
            'title' => 'My Test Reel',
        ]);

        $response = $this->get($signedUrl);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'video/mp4');
        $response->assertHeader('Content-Disposition', 'attachment; filename=my-test-reel.mp4');
        $response->assertStreamedContent('binary-video-content');
    }

    public function test_download_flow_handles_long_titles_without_validation_failure(): void
    {
        $longTitle = str_repeat('Very Long Instagram Reel Title ', 10);

        $this->mock(InstagramDownloaderInterface::class, function (MockInterface $mock) use ($longTitle): void {
            $mock->shouldReceive('getVideoData')
                ->once()
                ->andReturn([
                    'success' => true,
                    'video_url' => 'https://video.cdninstagram.com/reel.mp4',
                    'thumbnail' => null,
                    'title' => $longTitle,
                    'author' => 'Author',
                    'duration' => '10',
                ]);
        });

        $response = $this->from('/')->post('/download', [
            'url' => 'https://www.instagram.com/reel/abc123/',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('download_link');
        $response->assertSessionHasNoErrors();
    }

    public function test_preview_requires_valid_signature(): void
    {
        $this->get(route('preview', [
            'video_url' => 'https://video.cdninstagram.com/reel.mp4',
            'title' => 'Test Reel',
        ]))->assertStatus(403);
    }

    public function test_preview_page_renders_for_signed_request(): void
    {
        $signedUrl = URL::temporarySignedRoute('preview', now()->addMinutes(5), [
            'video_url' => 'https://video.cdninstagram.com/reel.mp4',
            'title' => 'Test Reel',
            'author' => 'Author',
            'duration' => '12',
        ]);

        $response = $this->get($signedUrl);

        $response->assertOk();
        $response->assertSee('Close preview');
        $response->assertSee('Test Reel');
    }
}
