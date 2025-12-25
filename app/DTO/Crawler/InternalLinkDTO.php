<?php

namespace App\DTO\Crawler;

/**
 * @psalm-immutable
 */
final readonly class InternalLinkDTO
{
    public function __construct(
        public string $targetUrl,
        public ?string $anchorText,
        public bool $nofollow,
    ) {}
}
