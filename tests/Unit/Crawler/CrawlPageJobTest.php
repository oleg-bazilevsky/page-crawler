<?php

namespace Tests\Unit\Crawler;

use App\Jobs\CrawlPageJob;
use App\Services\Crawler\PageImporter;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class CrawlPageJobTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function test_job_calls_importer(): void
    {
        $importer = $this->createMock(PageImporter::class);
        $importer->expects($this->once())
            ->method('import')
            ->with('https://example.com');

        $job = new CrawlPageJob('https://example.com');
        $job->handle($importer);
    }
}
