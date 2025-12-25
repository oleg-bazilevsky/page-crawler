<?php

use App\DTO\Crawler\CrawledPageDTO;
use App\Jobs\CrawlPageJob;
use App\Services\Crawler\CrawledPagePersister;
use App\Services\Crawler\PageImporter;
use App\Services\Crawler\RateLimiter;
use Mockery\MockInterface;

it('calls the importer when handling the job', function () {
    $importer = mock(PageImporter::class);
    $persister = mock(CrawledPagePersister::class);
    $rateLimiter = mock(RateLimiter::class);

    $rateLimiter
        ->shouldReceive('allow')
        ->with('https://example.com')
        ->once()
        ->andReturn(true);

    $importer
        ->shouldReceive('crawl')
        ->with('https://example.com')
        ->once()
        ->andReturn(
            mock(CrawledPageDTO::class)
        );

    $persister
        ->shouldReceive('persist')
        ->once();

    $job = new CrawlPageJob('https://example.com');
    $job->handle($importer, $persister, $rateLimiter);

});
