<?php

namespace Tests\Unit\Crawler;

use App\Models\Page;
use App\Services\Crawler\HtmlParser;
use App\Services\Crawler\HttpFetcher;
use App\Services\Crawler\PageImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

class PageImporterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws Exception
     */
    public function test_it_creates_a_page_record(): void
    {
        $fetcher = $this->createMock(HttpFetcher::class);
        $parser  = $this->createMock(HtmlParser::class);

        $fetcher->method('fetch')->willReturn('<html></html>');
        $parser->method('parse')->willReturn([
            'title'      => 'Title',
            'h1'         => 'H1',
            'body_text'  => 'Some content',
            'word_count' => 2,
        ]);

        $importer = new PageImporter($fetcher, $parser);

        $page = $importer->import('https://example.com');

        $this->assertInstanceOf(Page::class, $page);

        $this->assertDatabaseHas('pages', [
            'url'   => 'https://example.com',
            'title' => 'Title',
            'h1'    => 'H1',
        ]);
    }

    public function test_it_updates_existing_page(): void
    {
        Page::create([
            'url'        => 'https://example.com',
            'title'      => 'Old title',
            'h1'         => 'Old h1',
            'body_text'  => 'Old',
            'word_count' => 1,
        ]);

        $fetcher = $this->createMock(HttpFetcher::class);
        $parser  = $this->createMock(HtmlParser::class);

        $fetcher->method('fetch')->willReturn('<html></html>');
        $parser->method('parse')->willReturn([
            'title'      => 'New title',
            'h1'         => 'New h1',
            'body_text'  => 'New content',
            'word_count' => 2,
        ]);

        $importer = new PageImporter($fetcher, $parser);
        $importer->import('https://example.com');

        $this->assertDatabaseHas('pages', [
            'url'   => 'https://example.com',
            'title' => 'New title',
            'h1'    => 'New h1',
        ]);
    }

    public function test_it_returns_null_when_fetch_fails(): void
    {
        $fetcher = $this->createMock(HttpFetcher::class);
        $parser  = $this->createMock(HtmlParser::class);

        $fetcher->method('fetch')->willReturn(null);

        $importer = new PageImporter($fetcher, $parser);

        $result = $importer->import('https://example.com');

        $this->assertNull($result);
        $this->assertDatabaseCount('pages', 0);
    }
}
