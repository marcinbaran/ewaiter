<?php

namespace App\Services;

use App\Helpers\LanguageHelper;
use DeepL\Translator;

class TranslatorService
{

    private Translator $translator;

    public function __construct()
    {
        $this->translator = new Translator(env('DEEPL_API_KEY'));
    }

    public function translate(string $text, string $from, string $to): string
    {
        return $this->translator->translateText($text, LanguageHelper::convertLanguageCode($from), LanguageHelper::convertLanguageCode($to));
    }

    public function translateArray(array $texts, string $from, string $to): array
    {
        $translated = [];
        foreach ($texts as $text) {
            $translated[] = $this->translate($text, $from, $to);
        }

        return $translated;
    }

    public function translateArrayWithFallback(array $texts, string $from, string $to): array
    {
        $translated = [];
        foreach ($texts as $text) {
            $translated[] = $this->translate($text, $from, $to);
        }

        return $translated;
    }

}
