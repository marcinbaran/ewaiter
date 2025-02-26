<?php

namespace App\Decorators\AttributeGroups;

use App\Enum\AttributeGroupInputType;

class InputTypeDecorator
{
    public function decorate(AttributeGroupInputType $attributeGroupInputType)
    {
        switch ($attributeGroupInputType) {
            case AttributeGroupInputType::MULTIPLE_CONJUNCTIVE_OPTIONS_CHOICE:
                $styles = 'background-color: var(--yellow-200); color: var(--gray-900);';
                $text = __('attribute_groups.'.AttributeGroupInputType::MULTIPLE_CONJUNCTIVE_OPTIONS_CHOICE->value);
                break;
            case AttributeGroupInputType::MULTIPLE_ALTERNATIVE_OPTIONS_CHOICE:
                $styles = 'background-color: var(--blue-200); color: var(--gray-900);';
                $text = __('attribute_groups.'.AttributeGroupInputType::MULTIPLE_ALTERNATIVE_OPTIONS_CHOICE->value);
                break;
            case AttributeGroupInputType::SINGLE_OPTION_CHOICE:
                $styles = 'background-color: var(--green-200); color: var(--gray-900);';
                $text = __('attribute_groups.'.AttributeGroupInputType::SINGLE_OPTION_CHOICE->value);
                break;
        }

        return view('admin.partials.decorators.attribute_groups.input_type', ['styles' => $styles, 'text' => $text]);
    }
}
