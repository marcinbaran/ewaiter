<?php

namespace App\Decorators;

class StarDecorator
{
    public static function decorate($value): string
    {

        if (is_null($value)) {
            return '-';
        }
        $fullStars = $value;
        $emptyStars = 5 - $fullStars;

        $result = '<div class="flex">';

        for ($i = 0; $i < $fullStars; $i++) {
            $result .= '<svg width="22" height="21" viewBox="0 0 22 21" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M11.007 16.7538L4.82636 20L6.00701 13.1245L1 8.25567L7.90967 7.25531L11 1L14.0903 7.25531L21 8.25567L15.993 13.1245L17.1736 20L11.007 16.7538Z" fill="#FFD772" stroke="#E7AE1F" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
';
        }
        for ($i = 0; $i < $emptyStars; $i++) {
            $result .= '<svg width="22" height="21" viewBox="0 0 22 21" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M11.007 16.7538L4.82636 20L6.00701 13.1245L1 8.25567L7.90967 7.25531L11 1L14.0903 7.25531L21 8.25567L15.993 13.1245L17.1736 20L11.007 16.7538Z" stroke="#E7AE1F" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
';
        }
        $result .= '</div>';
        return $result;
    }
}
