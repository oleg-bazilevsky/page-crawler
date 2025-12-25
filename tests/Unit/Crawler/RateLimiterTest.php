<?php

use App\Services\Crawler\RateLimiter;
use Illuminate\Support\Facades\Redis;

beforeEach(function () {
    $store = [];
    $expirations = [];

    Redis::shouldReceive('incr')
        ->andReturnUsing(function ($key) use (&$store, &$expirations) {
            // If key is expired, reset count before incrementing
            if (isset($expirations[$key]) && ($expirations[$key] - time() <= 0)) {
                unset($store[$key]);
                unset($expirations[$key]);
            }

            $store[$key] = ($store[$key] ?? 0) + 1;
            return $store[$key];
        });

    Redis::shouldReceive('expire')
        ->andReturnUsing(function ($key, $seconds) use (&$expirations) {
            $expirations[$key] = time() + $seconds;
            return true;
        });

    Redis::shouldReceive('ttl')
        ->andReturnUsing(function ($key) use (&$expirations) {
            if (!isset($expirations[$key])) {
                return -1; // key doesn't exist
            }
            $remaining = $expirations[$key] - time();
            return $remaining > 0 ? $remaining : -2; // -2 means expired (real Redis behavior)
        });

    Redis::shouldReceive('del')
        ->andReturnUsing(function ($key) use (&$store, &$expirations) {
            unset($store[$key], $expirations[$key]);
            return 1;
        });

    Redis::shouldReceive('connection')->andReturnSelf();
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
