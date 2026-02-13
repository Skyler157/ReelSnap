<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Contracts\InstagramDownloaderInterface;
use App\Models\Download;
use App\Rules\InstagramUrl;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Throwable;

class ReelController extends Controller
{
    protected $downloader;

    public function __construct(InstagramDownloaderInterface $downloader)
    {
        $this->downloader = $downloader;
    }

    public function index()
    {
        return view('home');
    }

    public function download(Request $request)
    {
        $request->validate([
            'url' => [
                'required',
                'url',
                new InstagramUrl(),
            ]
        ]);

        try {
            $inputUrl = (string) $request->input('url');
            $result = $this->downloader->getVideoData($inputUrl);

            if (!is_array($result) || !array_key_exists('success', $result)) {
                Log::error('Downloader returned unexpected payload.', [
                    'payload_type' => gettype($result),
                ]);

                return back()->withErrors('Could not fetch the video. Please try again.');
            }

            if (!(bool) $result['success']) {
                $message = is_string($result['message'] ?? null)
                    ? $result['message']
                    : 'Could not fetch the video. Please try again.';

                return back()->withErrors($message);
            }

            Download::create([
                'url' => $this->normalizeUrlForStorage($inputUrl),
                'ip_address' => $this->hashIpAddress($request->ip()),
            ]);

            $safeTitle = $this->sanitizeTitle((string) ($result['title'] ?? 'Instagram Reel'));

            $downloadUrl = URL::temporarySignedRoute(
                'download.file',
                now()->addMinutes(10),
                [
                    'video_url' => (string) ($result['video_url'] ?? ''),
                    'title' => $safeTitle,
                ]
            );

            $previewUrl = URL::temporarySignedRoute(
                'preview',
                now()->addMinutes(10),
                [
                    'video_url' => (string) ($result['video_url'] ?? ''),
                    'title' => $safeTitle,
                    'author' => (string) ($result['author'] ?? 'Unknown'),
                    'duration' => (string) ($result['duration'] ?? 'N/A'),
                ]
            );

            return back()
                ->with('success', 'Video fetched successfully!')
                ->with('download_link', $downloadUrl)
                ->with('preview_link', $previewUrl)
                ->with('video', $result);
        } catch (Throwable $e) {
            Log::error('Download request failed.', [
                'exception' => $e::class,
                'message' => $e->getMessage(),
                'url' => (string) $request->input('url', ''),
            ]);

            return back()->withErrors('Unexpected server error. Please try again.');
        }
    }

    public function downloadFile(Request $request)
    {
        $validated = $request->validate([
            'video_url' => ['required', 'url'],
            'title' => ['nullable', 'string', 'max:120'],
        ]);

        $videoUrl = (string) $validated['video_url'];

        if (!$this->isSafeVideoUrl($videoUrl)) {
            abort(403);
        }

        try {
            $response = Http::withOptions(['stream' => true])
                ->connectTimeout((int) config('reelsnap.http.connect_timeout', 5))
                ->timeout((int) config('reelsnap.http.download_timeout', 120))
                ->get($videoUrl);

            if (!$response->successful()) {
                return back()->withErrors('Could not download the video. Please try again.');
            }

            $title = (string) ($validated['title'] ?? 'instagram-reel');
            $filename = Str::slug($title) ?: 'instagram-reel';
            $filename .= '.mp4';

            $contentType = $response->header('Content-Type', 'video/mp4');
            $contentLength = $response->header('Content-Length');
            $upstreamStream = $response->toPsrResponse()->getBody();

            $headers = [
                'Content-Type' => $contentType,
            ];

            if ($contentLength !== null) {
                $headers['Content-Length'] = $contentLength;
            }

            return response()->streamDownload(function () use ($upstreamStream): void {
                while (!$upstreamStream->eof()) {
                    echo $upstreamStream->read(1024 * 64);
                    flush();
                }
            }, $filename, $headers);
        } catch (Throwable) {
            return back()->withErrors('Could not download the video. Please try again.');
        }
    }

    public function preview(Request $request)
    {
        $validated = $request->validate([
            'video_url' => ['required', 'url'],
            'title' => ['nullable', 'string', 'max:120'],
            'author' => ['nullable', 'string', 'max:120'],
            'duration' => ['nullable', 'string', 'max:30'],
        ]);

        $videoUrl = (string) $validated['video_url'];

        if (!$this->isSafeVideoUrl($videoUrl)) {
            abort(403);
        }

        return view('preview', [
            'videoUrl' => $videoUrl,
            'title' => (string) ($validated['title'] ?? 'Instagram Reel'),
            'author' => (string) ($validated['author'] ?? 'Unknown'),
            'duration' => (string) ($validated['duration'] ?? 'N/A'),
        ]);
    }

    private function normalizeUrlForStorage(string $url): string
    {
        $parts = parse_url($url);

        if (!is_array($parts)) {
            return $url;
        }

        $scheme = strtolower($parts['scheme'] ?? 'https');
        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '';

        return $scheme . '://' . $host . $path;
    }

    private function hashIpAddress(?string $ipAddress): ?string
    {
        if ($ipAddress === null || $ipAddress === '') {
            return null;
        }

        return hash_hmac('sha256', $ipAddress, config('app.key', 'reelsnap'));
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

    private function sanitizeTitle(string $title): string
    {
        $trimmed = trim(preg_replace('/\s+/', ' ', $title) ?? '');

        if ($trimmed === '') {
            return 'Instagram Reel';
        }

        return Str::limit($trimmed, 120, '');
    }
}
