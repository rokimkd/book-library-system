<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use InvalidArgumentException;

class BookCoverService
{
    private const OPEN_LIBRARY_URL = 'https://covers.openlibrary.org/b/isbn/';
    private const RATE_LIMIT_KEY = 'open-library-api';
    private const RATE_LIMIT_ATTEMPTS = 5;
    private const VALID_SIZES = ['S', 'M', 'L'];

    private const HTTP_TIMEOUT = 10;
    private const HTTP_RETRY_TIMES = 3;
    private const HTTP_RETRY_SLEEP = 100;

    /**
     * Fetch book cover URL from Open Library
     *
     * @param string $isbn The book's ISBN
     * @param string $size Size of the cover image ('S', 'M', or 'L')
     * @return string|null The cover URL or null if not found/error
     * @throws InvalidArgumentException
     */
    public function fetchCoverUrl(string $isbn, string $size = 'M'): ?string
    {
        $this->validateInput($isbn, $size);

        if ($this->isRateLimited()) {
            return null;
        }

        try {
            $url = $this->buildCoverUrl($isbn, $size);
            $response = $this->makeHttpRequest($url);

            RateLimiter::hit(self::RATE_LIMIT_KEY);

            return $this->processResponse($response, $url);
        } catch (\Exception $e) {
            $this->logError($isbn, $e);
            return null;
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function validateInput(string $isbn, string $size): void
    {
        if (!in_array($size, self::VALID_SIZES, true)) {
            throw new InvalidArgumentException(
                sprintf('Invalid size parameter. Must be one of: %s', implode(', ', self::VALID_SIZES))
            );
        }
    }

    private function isRateLimited(): bool
    {
        if (RateLimiter::tooManyAttempts(self::RATE_LIMIT_KEY, self::RATE_LIMIT_ATTEMPTS)) {
            Log::warning('Open Library API rate limit exceeded');
            return true;
        }

        return false;
    }

    private function buildCoverUrl(string $isbn, string $size): string
    {
        $cleanIsbn = preg_replace('/[^0-9X]/', '', $isbn);
        return self::OPEN_LIBRARY_URL . $cleanIsbn . '-' . $size . '.jpg';
    }

    private function makeHttpRequest(string $url): Response
    {
        return Http::timeout(self::HTTP_TIMEOUT)
            ->retry(self::HTTP_RETRY_TIMES, self::HTTP_RETRY_SLEEP)
            ->get($url);
    }

    private function processResponse(Response $response, string $url): ?string
    {
        if ($response->successful() && $response->header('Content-Type') === 'image/jpeg') {
            return $url;
        }

        return null;
    }

    private function logError(string $isbn, \Exception $exception): void
    {
        Log::error(
            "Failed to fetch book cover for ISBN {$isbn}: " . $exception->getMessage(),
            ['exception' => $exception]
        );
    }
}
