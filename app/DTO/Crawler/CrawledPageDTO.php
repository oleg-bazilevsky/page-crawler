<?php

namespace App\DTO\Crawler;

/**
 * @psalm-immutable
 */
final readonly class CrawledPageDTO
{
    /**
     * @param InternalLinkDTO[] $internalLinks
     */
    public function __construct(
        public string $url,
        public ?string $title,
        public ?string $h1,
        public string $bodyText,
        public int $wordCount,
        public ?string $language,
        public array $internalLinks = [],
    ) {}
}
