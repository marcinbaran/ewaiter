<?php

use App\Enum\AttributeGroupInputType;

return [
    'id' => 'ID',
    'key' => 'Klucz',
    'name' => 'Nazwa',
    'description' => 'Opis',
    'is_primary' => 'Główna',
    'is_active' => 'Aktywna',
    'with_attributes' => 'Z atrybutami',
    'input_type' => 'Typ pola danych',
    'attribute_group_was_created' => 'Grupa atrybutów została utworzona',
    'attribute_group_was_updated' => 'Grupa atrybutów została zaktualizowana',
    'attribute_group_was_deleted' => 'Grupa atrybutów została usunięta',
    AttributeGroupInputType::MULTIPLE_ALTERNATIVE_OPTIONS_CHOICE->value => 'Wielokrotny wybór (zawiera jedno z zaznaczonych)',
    AttributeGroupInputType::MULTIPLE_CONJUNCTIVE_OPTIONS_CHOICE->value => 'Wielokrotny wybór (zawiera wszystkie zaznaczone)',
    AttributeGroupInputType::SINGLE_OPTION_CHOICE->value => 'Pojedynczy wybór',
    'is_attribute_group_active' => 'Czy grupa atrybutów jest aktywna?',
    'is_attribute_group_primary' => 'Czy grupa atrybutów jest główna?',
    'attribute_group_with_that_name_exists' => 'Grupa atrybutów o tej nazwie już istnieje',
    'only_one_primary_group_can_be_active' => 'Tylko jedna główna grupa atrybutów może być aktywna',
];
