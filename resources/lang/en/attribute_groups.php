<?php

use App\Enum\AttributeGroupInputType;

return [
    'attribute_group_was_created' => 'The attribute group has been created',
    'attribute_group_was_updated' => 'The attribute group has been updated',
    'attribute_group_was_deleted' => 'The attribute group has been deleted',
    'input_type' => 'Input type',
    AttributeGroupInputType::MULTIPLE_ALTERNATIVE_OPTIONS_CHOICE->value => 'Multiple choice (has one of selected)',
    AttributeGroupInputType::MULTIPLE_CONJUNCTIVE_OPTIONS_CHOICE->value => 'Multiple choice (has all selected)',
    AttributeGroupInputType::SINGLE_OPTION_CHOICE->value => 'Single choice',
    'is_attribute_group_active' => 'Is attribute group active?',
    'is_attribute_group_primary' => 'Is attribute group primary?',
];
