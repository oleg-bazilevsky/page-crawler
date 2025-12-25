<?php

use App\Services\Crawler\HttpFetcher;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\HandlerStack;

beforeEach(function () {
    $this->mockHandler = new MockHandler();
    $this->client = new Client([
        'handler' => HandlerStack::create($this->mockHandler),
    ]);
    $this->fetcher = new HttpFetcher($this->client);
});

it('returns HTML content when the response is 200 OK', function () {
    $this->mockHandler->append(
        new Response(200, [], '<html>OK</html>')
    );

    $result = $this->fetcher->fetch('https://example.com');

    expect($result)->toBe('<html>OK</html>');
});

it('returns null when the response is not 200 OK', function () {
    $this->mockHandler->append(
        new Response(404)
    );

    $result = $this->fetcher->fetch('https://example.com');

    expect($result)->toBeNull();
});

it('returns null when an exception occurs during fetch', function () {
    $this->mockHandler->append(
        new \GuzzleHttp\Exception\RequestException(
            'Connection failed',
            new \GuzzleHttp\Psr7\Request('GET', 'https://example.com')
        )
    );

    $result = $this->fetcher->fetch('https://example.com');

    expect($result)->toBeNull();
});
