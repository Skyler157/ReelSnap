<?php

namespace App\Services\Contracts;

interface InstagramDownloaderInterface
{
    public function getVideoData(string $url): array;
}