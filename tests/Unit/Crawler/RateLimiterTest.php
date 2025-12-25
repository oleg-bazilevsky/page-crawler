<?php

namespace Tests\Unit\Crawler;

use App\Services\Crawler\RateLimiter;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class RateLimiterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Redis::flushall();
    }

    public function test_allows_requests_under_limit(): void
    {
        $limiter = new RateLimiter(2, 10);

        $this->assertTrue($limiter->allow('https://example.com'));
        $this->assertTrue($limiter->allow('https://example.com'));
        $this->assertFalse($limiter->allow('https://example.com'));
    }

    public function test_limits_are_per_host(): void
    {
        $limiter = new RateLimiter(1, 10);

        $this->assertTrue($limiter->allow('https://example.com'));
        $this->assertTrue($limiter->allow('https://another.com'));
    }

    public function test_retry_after_returns_ttl(): void
    {
        $limiter = new RateLimiter(1, 10);

        $limiter->allow('https://example.com');
        $limiter->allow('https://example.com');

        $this->assertGreaterThan(0, $limiter->retryAfter('https://example.com'));
    }
}
