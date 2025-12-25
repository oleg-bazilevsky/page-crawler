<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUrlsRequest;
use App\Jobs\CrawlPageJob;
use Illuminate\Http\JsonResponse;

class CrawlController extends Controller
{
    /**
     * @param StoreUrlsRequest $request
     *
     * @return JsonResponse
     */
    public function crawl(StoreUrlsRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $urls = $validated['urls'];

        foreach ($urls as $url) {
            CrawlPageJob::dispatch($url);
        }

        return response()->json(['status' => 'queued']);
    }
}
