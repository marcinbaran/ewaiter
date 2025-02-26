<?php

namespace App\Http\Helpers;

class SearchHelper
{
    public static function replacePolishLetters(string $string): string
    {
        return str_replace(
            ['ą', 'ć', 'ę', 'ł', 'ń', 'ó', 'ś', 'ź', 'ż'],
            ['a', 'c', 'e', 'l', 'n', 'o', 's', 'z', 'z'],
            $string
        );
    }
}
