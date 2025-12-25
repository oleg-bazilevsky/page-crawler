<?php

use App\Services\Crawler\RateLimiter;
use Illuminate\Support\Facades\Redis;

// No "uses(Tests\TestCase::class)" needed!

beforeEach(function () {
    Redis::flushall();
});

it('allows requests under the rate limit', function () {
    $limiter = new RateLimiter(2, 10); // 2 requests per 10 seconds

    expect($limiter->allow('https://example.com'))->toBeTrue();
    expect($limiter->allow('https://example.com'))->toBeTrue();
    expect($limiter->allow('https://example.com'))->toBeFalse();
});

it('applies limits separately per host', function () {
    $limiter = new RateLimiter(1, 10); // 1 request per 10 seconds per host

    expect($limiter->allow('https://example.com'))->toBeTrue();
    expect($limiter->allow('https://example.com'))->toBeFalse();

    expect($limiter->allow('https://another.com'))->toBeTrue();
    expect($limiter->allow('https://another.com'))->toBeFalse();
});

it('returns the correct retry-after TTL when limit is exceeded', function () {
    $limiter = new RateLimiter(1, 10);

    $limiter->allow('https://example.com'); // First request allowed
    $limiter->allow('https://example.com'); // Second request blocked

    $retryAfter = $limiter->retryAfter('https://example.com');

    expect($retryAfter)->toBeInt();
    expect($retryAfter)->toBeGreaterThan(0);
    expect($retryAfter)->toBeLessThanOrEqual(10);
});

it('resets the limit after the window expires', function () {
    $limiter = new RateLimiter(1, 1); // 1 request per 1 second

    $limiter->allow('https://example.com'); // Allowed
    $limiter->allow('https://example.com'); // Denied

    sleep(2); // Wait longer than window

    expect($limiter->allow('https://example.com'))->toBeTrue();
});
