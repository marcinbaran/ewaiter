<?php

namespace App\Http\Requests\Admin;

use App\Enum\AttributeGroupInputType;
use App\Http\Requests\Api\AttributeGroupRequest as ApiAttributeGroupRequest;
use App\Rules\AttributeGroup\IsOnlyPrimaryActiveGroup;
use App\Rules\AttributeGroup\UniqueKey;

class AttributeGroupRequest extends ApiAttributeGroupRequest
{
    public function attributes()
    {
        return [
            self::ID_KEY => __('attribute_groups.id'),
            self::KEY_KEY => __('attribute_groups.key'),
            self::NAME_KEY => __('attribute_groups.name'),
            self::DESCRIPTION_KEY => __('attribute_groups.description'),
            self::INPUT_TYPE_KEY => __('attribute_groups.input_type'),
            self::IS_PRIMARY_KEY => __('attribute_groups.is_primary'),
            self::IS_ACTIVE_KEY => __('attribute_groups.is_active'),
            self::WITH_ATTRIBUTES_KEY => __('attribute_groups.with_attributes'),

        ];
    }

    public function rules()
    {
        $rules = [
            self::METHOD_GET => [
                self::ID_KEY => 'nullable|int',
                self::WITH_ATTRIBUTES_KEY => 'nullable|boolean',
            ],
            self::METHOD_POST => [
                self::KEY_KEY => ['required', 'string', 'max:255', new UniqueKey()],
                self::NAME_KEY => 'required|array|min:1|max:30',
                self::NAME_KEY.'.*' => 'nullable|string|min:3|max:50',
                self::NAME_KEY.'.pl' => 'required|string|min:3|max:50',
                self::DESCRIPTION_KEY => 'nullable|array|min:1|max:30',
                self::DESCRIPTION_KEY.'.*' => 'nullable|string|min:3|max:1000',
                self::INPUT_TYPE_KEY => 'required|string|in:'.AttributeGroupInputType::getValuesForRequestRule(),
                self::IS_PRIMARY_KEY => ['required', 'boolean', new IsOnlyPrimaryActiveGroup()],
                self::IS_ACTIVE_KEY => 'required|boolean',
            ],
            self::METHOD_PUT => [
                self::KEY_KEY => ['required', 'string', 'max:255', new UniqueKey()],
                self::NAME_KEY => 'required|array|min:1|max:30',
                self::NAME_KEY.'.*' => 'nullable|string|min:3|max:50',
                self::NAME_KEY.'.pl' => 'required|string|min:3|max:50',
                self::DESCRIPTION_KEY => 'nullable|array|min:1|max:30',
                self::DESCRIPTION_KEY.'.*' => 'nullable|string|min:3|max:1000',
                self::INPUT_TYPE_KEY => 'required|string|in:'.AttributeGroupInputType::getValuesForRequestRule(),
                self::IS_PRIMARY_KEY => ['required', 'boolean', new IsOnlyPrimaryActiveGroup()],
                self::IS_ACTIVE_KEY => 'required|boolean',
            ],
            self::METHOD_DELETE => [
                self::ID_KEY => 'required|int',
            ],
        ];

        return $rules[$this->getMethod()];
    }
}
