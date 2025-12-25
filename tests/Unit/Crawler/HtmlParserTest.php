<?php

use App\Services\Crawler\HtmlParser;

beforeEach(function () {
    $this->parser = new HtmlParser();
});

it('extracts title, h1, and body text correctly', function () {
    $html = <<<'HTML'
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

    expect($result)
        ->toBeArray()
        ->title->toBe('Test Page')
        ->h1->toBe('Main Heading')
        ->body_text->toBe('Main Heading This is some text. More text here.')
        ->word_count->toBe(9);
});

it('handles missing elements gracefully', function () {
    $html = '<html><body><p>Only text</p></body></html>';

    $result = $this->parser->parse($html);

    expect($result)
        ->toBeArray()
        ->title->toBeNull()
        ->h1->toBeNull()
        ->body_text->toBe('Only text')
        ->word_count->toBe(2);
});

it('handles empty or malformed HTML', function () {
    $result = $this->parser->parse('');

    expect($result)
        ->toBeArray()
        ->title->toBeNull()
        ->h1->toBeNull()
        ->body_text->toBe('')
        ->word_count->toBe(0);
});
