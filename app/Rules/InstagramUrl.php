<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InstagramUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || !$this->isValidInstagramUrl($value)) {
            $fail('The :attribute must be a valid Instagram reel or post URL.');
        }
    }

    private function isValidInstagramUrl(string $url): bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '';

        if (!in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        $allowedHosts = ['instagram.com', 'www.instagram.com', 'm.instagram.com'];

        if (!in_array($host, $allowedHosts, true)) {
            return false;
        }

        return (bool) preg_match('#^/(reel|p)/[^/]+/?$#', $path);
    }
}
