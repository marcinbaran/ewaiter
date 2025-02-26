<?php

namespace App\Enum;

enum AttributeGroupInputType: string
{
    use EnumTrait;
    case SINGLE_OPTION_CHOICE = 'radio';
    case MULTIPLE_ALTERNATIVE_OPTIONS_CHOICE = 'checkbox_or';
    case MULTIPLE_CONJUNCTIVE_OPTIONS_CHOICE = 'checkbox_and';
}
