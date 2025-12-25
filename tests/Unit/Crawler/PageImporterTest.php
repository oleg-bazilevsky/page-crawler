<?php

use App\DTO\Crawler\CrawledPageDTO;
use App\Models\Page;
use App\Services\Crawler\CrawledPagePersister;
use App\Services\Crawler\HtmlParser;
use App\Services\Crawler\HttpFetcher;
use App\Services\Crawler\InternalLinkExtractor;
use App\Services\Crawler\PageImporter;
use App\Services\Language\LanguageDetector;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Psr\Http\Message\RequestInterface;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->request = mock(RequestInterface::class);
    $this->fetcher = mock(HttpFetcher::class);
    $this->parser = mock(HtmlParser::class);
    $this->languageDetector = mock(LanguageDetector::class);
    $this->internalLinkExtractor = mock(InternalLinkExtractor::class);

    $this->importer = new PageImporter(
        $this->fetcher,
        $this->parser,
        $this->languageDetector,
        $this->internalLinkExtractor
    );

    $this->persister = app(CrawledPagePersister::class);
});

it('creates a new page record when crawling a URL', function () {
    $this->fetcher
        ->shouldReceive('fetch')
        ->with('https://example.com')
        ->once()
        ->andReturn('<html></html>');

    $this->parser
        ->shouldReceive('parse')
        ->with('<html></html>')
        ->once()
        ->andReturn([
            'title' => 'Title',
            'h1' => 'H1',
            'body_text' => 'Some content',
            'word_count' => 2,
        ]);

    $this->languageDetector
        ->shouldReceive('detect')
        ->with('Some content')
        ->once()
        ->andReturn('en');

    $this->internalLinkExtractor
        ->shouldReceive('extract')
        ->with('<html></html>', 'https://example.com')
        ->once()
        ->andReturn([]);

    $dto = $this->importer->crawl('https://example.com');

    expect($dto)->toBeInstanceOf(CrawledPageDTO::class);

    $this->persister->persist($dto);

    $this->assertDatabaseHas('pages', [
        'url' => 'https://example.com',
        'title' => 'Title',
        'h1' => 'H1',
        'language' => 'en',
    ]);
});

it('updates an existing page record when crawling the same URL again', function () {
    Page::create([
        'url' => 'https://example.com',
        'title' => 'Old title',
        'h1' => 'Old h1',
        'body_text' => 'Old',
        'word_count' => 1,
        'language' => 'en',
    ]);

    $this->fetcher
        ->shouldReceive('fetch')
        ->with('https://example.com')
        ->once()
        ->andReturn('<html></html>');

    $this->parser
        ->shouldReceive('parse')
        ->with('<html></html>')
        ->once()
        ->andReturn([
            'title' => 'New title',
            'h1' => 'New h1',
            'body_text' => 'New content',
            'word_count' => 2,
        ]);

    $this->languageDetector
        ->shouldReceive('detect')
        ->with('New content')
        ->once()
        ->andReturn('en');

    $this->internalLinkExtractor
        ->shouldReceive('extract')
        ->with('<html></html>', 'https://example.com')
        ->once()
        ->andReturn([]);

    $dto = $this->importer->crawl('https://example.com');

    expect($dto)->toBeInstanceOf(CrawledPageDTO::class);

    $this->persister->persist($dto);

    $this->assertDatabaseHas('pages', [
        'url' => 'https://example.com',
        'title' => 'New title',
        'h1' => 'New h1',
        'language' => 'en',
    ]);
});

it('returns null and does not create a record when fetching fails', function () {
    $this->fetcher
        ->shouldReceive('fetch')
        ->with('https://example.com')
        ->once()
        ->andReturn(null);

    $result = $this->importer->crawl('https://example.com');

    expect($result)->toBeNull();

    $this->assertDatabaseCount('pages', 0);
});

it('returns null when an exception occurs during fetch', function () {
    $this->fetcher
        ->shouldReceive('fetch')
        ->with('https://example.com')
        ->once()
        ->andThrow(new RequestException('Failed to fetch', $this->request));

    $result = $this->importer->crawl('https://example.com');

    expect($result)->toBeNull();
    $this->assertDatabaseCount('pages', 0);
});
