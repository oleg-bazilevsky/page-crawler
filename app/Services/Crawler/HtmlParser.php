<?php

namespace App\Services\Crawler;

use Symfony\Component\DomCrawler\Crawler;

class HtmlParser
{
    /**
     * @param string $html
     *
     * @return array
     */
    public function parse(string $html): array
    {
        $crawler = new Crawler($html);

        $title = $crawler->filter('title')->count()
            ? trim($crawler->filter('title')->text())
            : null;

        $h1 = $crawler->filter('h1')->count()
            ? trim($crawler->filter('h1')->first()->text())
            : null;

        // Remove non-content areas
        foreach (['script', 'style', 'nav', 'footer', 'header'] as $selector) {
            $crawler->filter($selector)->each(fn ($node) => $node->getNode(0)->parentNode->removeChild($node->getNode(0)));
        }

        $bodyText = trim(preg_replace('/\s+/u', ' ', $crawler->filter('body')->text('')));

        return [
            'title'      => $title,
            'h1'         => $h1,
            'body_text'  => $bodyText,
            'word_count' => str_word_count($bodyText),
        ];
    }
}
