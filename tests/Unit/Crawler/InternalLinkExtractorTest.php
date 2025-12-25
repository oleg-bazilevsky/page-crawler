<?php

namespace Tests\Unit\Crawler;

use App\Services\Crawler\InternalLinkExtractor;
use PHPUnit\Framework\TestCase;

class InternalLinkExtractorTest extends TestCase
{
    public function test_extracts_only_internal_links(): void
    {
        $html = <<<HTML
<a href="/about">About</a>
<a href="https://example.com/contact">Contact</a>
<a href="https://google.com">External</a>
<a href="#anchor">Skip</a>
HTML;

        $extractor = new InternalLinkExtractor();

        $links = $extractor->extract($html, 'https://example.com');

        $this->assertCount(2, $links);

        $this->assertSame('/about', $links[0]['target_url']);
        $this->assertSame('About', $links[0]['anchor_text']);
    }

    public function test_detects_nofollow(): void
    {
        $html = '<a href="/test" rel="nofollow">Test</a>';

        $extractor = new InternalLinkExtractor();
        $links = $extractor->extract($html, 'https://example.com');

        $this->assertTrue($links[0]['nofollow']);
    }
}
