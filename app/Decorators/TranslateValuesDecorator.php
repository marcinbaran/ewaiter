<?php

namespace App\Decorators;

class TranslateValuesDecorator
{
    /**
     * @param array $values
     * @param string $flagSize
     * @return string
     */
    public function decorate(array $values, string $flagSize = '5')
    {
        $string = '';
        foreach ($values as $lang => $label) {
            $string .= sprintf(
                '<p class="flex"><img src="/images/flags/%s.png" alt="%s" class="mr-2 h-%s" /> %s</p>',
                $lang,
                'flag-'.$lang,
                $flagSize,
                $label
            );
        }

        return $string;
    }
}
