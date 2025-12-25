<?php

namespace App\Services\Language;

class LanguageDetector
{
    private array $languages = [
        'en' => ['the', 'and', 'of', 'to', 'in', 'is', 'that', 'it', 'for'],
        'de' => ['der', 'die', 'und', 'in', 'den', 'von', 'zu', 'das'],
        'fr' => ['le', 'la', 'et', 'les', 'des', 'en', 'un', 'une'],
        'es' => ['el', 'la', 'y', 'los', 'de', 'en', 'un', 'una'],
        'it' => ['il', 'lo', 'e', 'di', 'che', 'la', 'un'],
        'ru' => ['и', 'в', 'не', 'на', 'что', 'я', 'он'],
        'sr' => ['i', 'da', 'je', 'u', 'se', 'na', 'za'],
    ];

    /**
     * @param string $text
     *
     * @return string|null
     */
    public function detect(string $text): ?string
    {
        $text = mb_strtolower($this->normalize($text));

        if (mb_strlen($text) < 100) {
            return null;
        }

        $scores = [];

        foreach ($this->languages as $lang => $stopWords) {
            $scores[$lang] = 0;

            foreach ($stopWords as $word) {
                $scores[$lang] += preg_match_all(
                    '/\b' . preg_quote($word, '/') . '\b/u',
                    $text
                );
            }
        }

        arsort($scores);

        $topLang = array_key_first($scores);

        return $scores[$topLang] > 0 ? $topLang : null;
    }

    /**
     * @param string $text
     *
     * @return string
     */
    private function normalize(string $text): string
    {
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[^\p{L}\s]/u', ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text);
    }
}
