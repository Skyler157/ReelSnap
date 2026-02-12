<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Contracts\InstagramDownloaderInterface;
use App\Models\Download;

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
                'regex:/instagram\.com\/(reel|p)\//'
            ]
        ]);

        $result = $this->downloader->getVideoData($request->url);

        if (!$result['success']) {
            return back()->withErrors($result['message']);
        }

        Download::create([
            'url' => $request->url,
            'ip_address' => $request->ip(),
        ]);

        return back()
            ->with('success', 'Video fetched successfully!')
            ->with('video', $result);
    }
}