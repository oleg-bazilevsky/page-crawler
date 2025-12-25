<?php

namespace Tests\Unit\Language;

use App\Services\Language\LanguageDetector;
use PHPUnit\Framework\TestCase;

class LanguageDetectorTest extends TestCase
{
    private LanguageDetector $detector;

    protected function setUp(): void
    {
        $this->detector = new LanguageDetector();
    }

    public function test_detects_english(): void
    {
        $text = str_repeat(
            'This is the content of the page and it is written in English. ',
            10
        );

        $this->assertSame('en', $this->detector->detect($text));
    }

    public function test_detects_serbian(): void
    {
        $text = str_repeat(
            'Ovo je sadržaj stranice i napisan je na engleskom jeziku. ',
            10
        );

        $this->assertSame('sr', $this->detector->detect($text));
    }

    public function test_returns_null_for_short_text(): void
    {
        $this->assertNull($this->detector->detect('Hello world'));
    }

    public function test_returns_null_for_unknown_language(): void
    {
        $text = str_repeat('xyz abc qwe rty ', 20);

        $this->assertNull($this->detector->detect($text));
    }
}
