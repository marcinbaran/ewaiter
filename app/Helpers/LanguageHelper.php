<?php

namespace App\Helpers;

class LanguageHelper
{
    /**
     * Zamienia kod językowy na odpowiedni kod regionalny.
     *
     * @param string $languageCode
     * @return string
     */
    public static function convertLanguageCode($languageCode)
    {
        $languageMap = [
            'en' => 'en-US',
            //'pl' => 'pl-PL',
//            'de' => 'de-DE',
//            'fr' => 'fr-FR',
//            'ru' => 'ru-RU',
//            'es' => 'es-ES',
//            'it' => 'it-IT',
//            'pt' => 'pt-PT',
//            'nl' => 'nl-NL',
            'sv' => 'sv-SE',
            'fi' => 'fi-FI',
            'no' => 'no-NO',
            'da' => 'da-DK',
            'cs' => 'cs-CZ',
            'hu' => 'hu-HU',
            'tr' => 'tr-TR',
            'ja' => 'ja-JP',
            'zh' => 'zh-CN',
            'ko' => 'ko-KR',
            'ar' => 'ar-SA',
            'he' => 'he-IL',
            'el' => 'el-GR',
            'th' => 'th-TH',
            'id' => 'id-ID',
            'ms' => 'ms-MY',
        ];

        if (array_key_exists($languageCode, $languageMap)) {
            return $languageMap[$languageCode];
        }

        return $languageCode;
    }
}
