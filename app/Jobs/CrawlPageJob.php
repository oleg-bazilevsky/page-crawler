<?php

namespace App\Jobs;

use App\Services\Crawler\CrawledPagePersister;
use App\Services\Crawler\PageImporter;
use App\Services\Crawler\RateLimiter;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class CrawlPageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public function __construct(private readonly string $url) {}

    /**
     * @param PageImporter $importer
     * @param CrawledPagePersister $persister
     * @param RateLimiter $rateLimiter
     *
     * @return void
     * @throws GuzzleException
     * @throws Throwable
     */
    public function handle(
        PageImporter $importer,
        CrawledPagePersister $persister,
        RateLimiter $rateLimiter
    ): void {
        if (!$rateLimiter->allow($this->url)) {
            $this->release($rateLimiter->retryAfter($this->url));
            return;
        }
        $dto = $importer->crawl($this->url);

        if ($dto === null) {
            return;
        }

        $persister->persist($dto);
    }
}
