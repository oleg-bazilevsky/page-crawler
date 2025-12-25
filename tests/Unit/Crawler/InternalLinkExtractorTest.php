<?php

use App\Services\Crawler\InternalLinkExtractor;

beforeEach(function () {
    $this->extractor = new InternalLinkExtractor();
});

it('extracts only internal links and ignores external and anchor links', function () {
    $html = <<<'HTML'
<a href="/about">About</a>
<a href="https://example.com/contact">Contact</a>
<a href="https://google.com">External</a>
<a href="#anchor">Skip</a>
<a href="https://example.com/blog">Blog</a>
HTML;

    $links = $this->extractor->extract($html, 'https://example.com');
    expect($links)->toBeArray()->toHaveCount(3);

    expect($links[0])
        ->target_url->toBe('/about')
        ->anchor_text->toBe('About');

    expect($links[1])
        ->target_url->toBe('/contact')
        ->anchor_text->toBe('Contact');

    expect($links[2])
        ->target_url->toBe('/blog')
        ->anchor_text->toBe('Blog');
});

it('correctly detects nofollow attribute', function () {
    $html = <<<'HTML'
<a href="/test" rel="nofollow">Test Link</a>
<a href="/normal">Normal</a>
<a href="/nofollow" rel="nofollow sponsored">Sponsored</a>
HTML;

    $links = $this->extractor->extract($html, 'https://example.com');

    expect($links)->toBeArray()->toHaveCount(3);

    expect($links[0])->nofollow->toBeTrue();
    expect($links[1])->nofollow->toBeFalse();
    expect($links[2])->nofollow->toBeTrue();
});
