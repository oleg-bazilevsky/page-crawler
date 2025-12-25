<?php

namespace App\Services\Crawler;

use Symfony\Component\DomCrawler\Crawler;

class InternalLinkExtractor
{
    /**
     * @param string $html
     * @param string $baseUrl
     *
     * @return array
     */
    public function extract(string $html, string $baseUrl): array
    {
        $crawler = new Crawler($html, $baseUrl);

        $baseHost = parse_url($baseUrl, PHP_URL_HOST);

        $links = [];

        $crawler->filter('a[href]')->each(function (Crawler $node) use (&$links, $baseHost) {
            $href = trim($node->attr('href'));

            if ($href === '' || str_starts_with($href, '#')) {
                return;
            }

            $absoluteUrl = $this->normalizeUrl($href, $baseHost);

            if (!$absoluteUrl) {
                return;
            }

            $links[] = [
                'target_url' => $absoluteUrl,
                'anchor_text' => $this->anchorText($node),
                'nofollow' => $this->isNoFollow($node),
            ];
        });

        return $links;
    }

    /**
     * @param string $href
     * @param string $baseHost
     *
     * @return string|null
     */
    private function normalizeUrl(string $href, string $baseHost): ?string
    {
        if (preg_match('#^(mailto|tel|javascript):#i', $href)) {
            return null;
        }

        $url = parse_url($href);

        if (isset($url['host']) && $url['host'] !== $baseHost) {
            return null;
        }

        $path = $url['path'] ?? '/';
        return rtrim($path, '/') ?: '/';
    }

    /**
     * @param Crawler $node
     *
     * @return string|null
     */
    private function anchorText(Crawler $node): ?string
    {
        $text = trim(preg_replace('/\s+/u', ' ', $node->text('')));

        return $text !== '' ? $text : null;
    }

    /**
     * @param Crawler $node
     *
     * @return bool
     */
    private function isNoFollow(Crawler $node): bool
    {
        $rel = strtolower((string) $node->attr('rel'));

        return str_contains($rel, 'nofollow');
    }
}
