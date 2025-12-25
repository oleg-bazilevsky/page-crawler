<?php

namespace App\Console\Commands;

use App\DTO\Crawler\CrawledPageDTO;
use App\Services\Crawler\CrawledPagePersister;
use App\Services\Crawler\PageImporter;
use App\Services\Crawler\RateLimiter;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Throwable;

class CrawlPages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl-pages {urls : List of pages} }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It crawls needed pages';

    public function __construct(private PageImporter $importer,
                                private CrawledPagePersister $persister,
                                private RateLimiter $rateLimiter) {
        parent::__construct();
    }

    /**
     * @return void
     * @throws GuzzleException|Throwable
     */
    public function handle(): void
    {
        $urls = explode(',', $this->argument('urls'));

        foreach ($urls as $url) {
            if (!$this->rateLimiter->allow($url)) {
                return;
            }
            $dto = $this->importer->crawl($url);

            if (!$dto instanceof CrawledPageDTO) {
                return;
            }

            $this->persister->persist($dto);
        }
    }
}
