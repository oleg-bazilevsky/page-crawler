<?php
namespace App\Services\Crawler;

use App\Models\Page;
use Illuminate\Support\Facades\DB;
use Throwable;

class InternalLinkPersister
{
    /**
     * @param Page $page
     * @param array $links
     *
     * @return void
     * @throws Throwable
     */
    public function persist(Page $page, array $links): void
    {
        DB::transaction(function () use ($page, $links) {
            $page->internalLinks()->delete();

            foreach ($links as $link) {
                $page->internalLinks()->create($link);
            }
        });
    }
}
