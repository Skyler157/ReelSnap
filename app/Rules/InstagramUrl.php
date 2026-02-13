<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InstagramUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || !$this->isValidInstagramUrl(trim($value), 0)) {
            $fail('The :attribute must be a valid Instagram reel or post URL.');
        }
    }

    private function isValidInstagramUrl(string $url, int $depth): bool
    {
        if ($depth > 2) {
            return false;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host = strtolower($parts['host'] ?? '');
        $path = strtolower($parts['path'] ?? '');
        $query = $parts['query'] ?? '';

        if (!in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        // Instagram commonly wraps share links with l.instagram.com?u=...
        if ($host === 'l.instagram.com') {
            parse_str($query, $queryParams);
            $targetUrl = $queryParams['u'] ?? $queryParams['url'] ?? null;

            return is_string($targetUrl) && $this->isValidInstagramUrl($targetUrl, $depth + 1);
        }

        $allowedHosts = ['instagram.com', 'www.instagram.com', 'm.instagram.com'];

        if (!in_array($host, $allowedHosts, true)) {
            return false;
        }

        // Supported formats:
        // - /reel/{id}
        // - /reels/{id}
        // - /p/{id}
        // - /tv/{id}
        // - /share/reel/{id}
        return (bool) preg_match('#^/(reels?|p|tv)/[^/?#]+/?$#', $path)
            || (bool) preg_match('#^/share/reel/[^/?#]+/?$#', $path);
    }
}
