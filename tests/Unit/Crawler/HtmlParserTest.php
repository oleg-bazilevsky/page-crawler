<?php

namespace Tests\Unit\Crawler;

use App\Services\Crawler\HtmlParser;
use PHPUnit\Framework\TestCase;

class HtmlParserTest extends TestCase
{
    private HtmlParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new HtmlParser();
    }

    public function test_it_extracts_title_h1_and_body_text(): void
    {
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>Test Page</title>
</head>
<body>
    <header>Header content</header>
    <nav>Navigation</nav>

    <h1>Main Heading</h1>
    <p>This is some text.</p>
    <p>More text here.</p>

    <footer>Footer content</footer>
</body>
</html>
HTML;

        $result = $this->parser->parse($html);

        $this->assertSame('Test Page', $result['title']);
        $this->assertSame('Main Heading', $result['h1']);
        $this->assertSame('This is some text. More text here.', $result['body_text']);
        $this->assertSame(6, $result['word_count']);
    }

    public function test_it_handles_missing_elements_gracefully(): void
    {
        $html = '<html><body><p>Only text</p></body></html>';

        $result = $this->parser->parse($html);

        $this->assertNull($result['title']);
        $this->assertNull($result['h1']);
        $this->assertSame('Only text', $result['body_text']);
        $this->assertSame(2, $result['word_count']);
    }
}
