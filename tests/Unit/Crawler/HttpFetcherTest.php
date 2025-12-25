<?php

namespace Tests\Unit\Crawler;

use App\Services\Crawler\HttpFetcher;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\HandlerStack;
use PHPUnit\Framework\TestCase;

class HttpFetcherTest extends TestCase
{
    public function test_fetch_returns_html_on_200(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '<html>OK</html>')
        ]);

        $client = new Client([
            'handler' => HandlerStack::create($mock),
        ]);

        $fetcher = new HttpFetcher($client);

        $result = $fetcher->fetch('https://example.com');

        $this->assertSame('<html>OK</html>', $result);
    }

    public function test_fetch_returns_null_on_non_200(): void
    {
        $mock = new MockHandler([
            new Response(404)
        ]);

        $client = new Client([
            'handler' => HandlerStack::create($mock),
        ]);

        $fetcher = new HttpFetcher($client);

        $this->assertNull($fetcher->fetch('https://example.com'));
    }
}
