<?php

namespace App\DTO\Crawler;

class CrawlFailureDTO
{
    public function __construct(
        public string $url,
        public string $reason,
    ) {}
}
