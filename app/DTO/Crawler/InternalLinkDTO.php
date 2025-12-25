<?php

namespace App\DTO\Crawler;

/**
 * @psalm-immutable
 */
class InternalLinkDTO
{
    public function __construct(
        public string $targetUrl,
        public ?string $anchorText,
        public bool $nofollow,
    ) {}
}
