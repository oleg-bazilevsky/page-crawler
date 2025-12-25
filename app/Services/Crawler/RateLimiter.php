<?php

namespace App\Services\Crawler;

use Illuminate\Support\Facades\Redis;

class RateLimiter
{
    public function __construct(
        private readonly int $maxRequests = 5,
        private int          $windowSeconds = 10
    ) {}

    /**
     * @param string $url
     *
     * @return bool
     */
    public function allow(string $url): bool
    {
        $host = $this->host($url);
        $key  = $this->key($host);

        $current = Redis::incr($key);

        if ($current === 1) {
            Redis::expire($key, $this->windowSeconds);
        }

        return $current <= $this->maxRequests;
    }

    /**
     * @param string $url
     *
     * @return int
     */
    public function retryAfter(string $url): int
    {
        $ttl = Redis::ttl($this->key($this->host($url)));

        return $ttl > 0 ? $ttl : $this->windowSeconds;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    private function host(string $url): string
    {
        return parse_url($url, PHP_URL_HOST) ?? 'unknown';
    }

    /**
     * @param string $host
     *
     * @return string
     */
    private function key(string $host): string
    {
        return "crawler:rate:{$host}";
    }
}
