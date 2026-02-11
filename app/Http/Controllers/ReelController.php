<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Contracts\InstagramDownloaderInterface;

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
            'url' => 'required|url'
        ]);

        $result = $this->downloader->getVideoData($request->url);

        return back()
            ->with('success', 'Video fetched successfully!')
            ->with('video', $result);
    }
}