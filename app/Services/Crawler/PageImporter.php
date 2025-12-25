<?php

namespace App\Services\Crawler;

use App\Services\Language\LanguageDetector;
use App\DTO\Crawler\CrawledPageDTO;
use App\DTO\Crawler\InternalLinkDTO;
use GuzzleHttp\Exception\GuzzleException;

class PageImporter
{
    public function __construct(
        private HttpFetcher           $fetcher,
        private HtmlParser            $parser,
        private LanguageDetector      $languageDetector,
        private InternalLinkExtractor $linkExtractor
    ) {}

    /**
     * @param string $url
     *
     * @return CrawledPageDTO|null
     */
    public function crawl(string $url): ?CrawledPageDTO
    {
        try {
            $html = $this->fetcher->fetch($url);
        } catch (GuzzleException $e) {
            return null;
        }

        if (!$html) {
            return null;
        }

        $parsed = $this->parser->parse($html);

        $language = $this->languageDetector
            ->detect($parsed['body_text'] ?? '');

        $links = $this->linkExtractor->extract($html, $url);

        $linkDTOs = array_map(
            fn (array $link) => new InternalLinkDTO(
                targetUrl: $link['target_url'],
                anchorText: $link['anchor_text'],
                nofollow: $link['nofollow'],
            ),
            $links
        );

        return new CrawledPageDTO(
            url: $url,
            title: $parsed['title'],
            h1: $parsed['h1'],
            bodyText: $parsed['body_text'],
            wordCount: $parsed['word_count'],
            language: $language,
            internalLinks: $linkDTOs,
        );
    }
}
