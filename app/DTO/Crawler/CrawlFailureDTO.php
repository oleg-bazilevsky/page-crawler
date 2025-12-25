<?php

namespace App\DTO\Crawler;

final readonly class CrawlFailureDTO
{
    public function __construct(
        public string $url,
        public string $reason,
    ) {}
}
