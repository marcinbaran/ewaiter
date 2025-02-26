<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Api\AttributeRequest as ApiAttributeRequest;
use App\Rules\Attribute\UniqueKey;

class AttributeRequest extends ApiAttributeRequest
{
    public function rules()
    {
        $rules = [
            self::METHOD_GET => [
                self::ID_KEY => 'nullable|int',
                self::WITH_ATTRIBUTE_GROUP_KEY => 'nullable|boolean',
            ],
            self::METHOD_POST => [
                self::KEY_KEY => ['required', 'string', 'max:255', new UniqueKey()],
                self::NAME_KEY => 'required|array|min:1|max:30',
                self::NAME_KEY.'.*' => 'nullable|string|min:3|max:50',
                self::NAME_KEY.'.pl' => 'required|string|min:3|max:50',
                self::DESCRIPTION_KEY => 'nullable|array|min:1|max:30',
                self::DESCRIPTION_KEY.'.*' => 'nullable|string|min:3|max:1000',
                self::ICON_KEY => 'nullable|string|max:255',
                self::IS_ACTIVE_KEY => 'required|boolean',
                self::ATTRIBUTE_GROUP_ID_KEY => 'nullable|int',
            ],
            self::METHOD_PUT => [
                self::KEY_KEY => ['required', 'string', 'max:255', new UniqueKey()],
                self::NAME_KEY => 'required|array|min:1|max:30',
                self::NAME_KEY.'.*' => 'nullable|string|min:3|max:50',
                self::NAME_KEY.'.pl' => 'required|string|min:3|max:50',
                self::DESCRIPTION_KEY => 'nullable|array|min:1|max:30',
                self::DESCRIPTION_KEY.'.*' => 'nullable|string|min:3|max:1000',
                self::ICON_KEY => 'nullable|string|max:255',
                self::IS_ACTIVE_KEY => 'required|boolean',
                self::ATTRIBUTE_GROUP_ID_KEY => 'nullable|int',
            ],
            self::METHOD_DELETE => [
                self::ID_KEY => 'required|int',
            ],
        ];

        return $rules[$this->getMethod()];
    }
}
