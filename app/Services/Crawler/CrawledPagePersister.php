<?php

namespace App\Services\Crawler;

use App\DTO\Crawler\CrawledPageDTO;
use App\Models\Page;
use Illuminate\Support\Facades\DB;
use Throwable;

class CrawledPagePersister
{
    /**
     * @throws Throwable
     */
    public function persist(CrawledPageDTO $dto): Page
    {
        return DB::transaction(function () use ($dto) {
            $page = Page::updateOrCreate(
                ['url' => $dto->url],
                [
                    'title'      => $dto->title,
                    'h1'         => $dto->h1,
                    'body_text'  => $dto->bodyText,
                    'word_count' => $dto->wordCount,
                    'language'   => $dto->language,
                ]
            );

            $page->internalLinks()->delete();

            foreach ($dto->internalLinks as $link) {
                $page->internalLinks()->create([
                    'target_url' => $link->targetUrl,
                    'anchor_text' => $link->anchorText,
                    'nofollow' => $link->nofollow,
                ]);
            }

            return $page;
        });
    }
}
