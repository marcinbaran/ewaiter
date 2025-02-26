<?php

namespace App\Rules\AttributeGroup;

use App\Models\AttributeGroup;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueKey implements ValidationRule, DataAwareRule
{
    protected $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $attributeGroupId = request()->route('attribute_group')->id ?? null;
        if ($attributeGroupId) {
            $isAttributeGroupWithTheSameKeyExists = AttributeGroup::where('key', $value)->where('id', '!=', $attributeGroupId)->exists();
            if ($isAttributeGroupWithTheSameKeyExists) {
                $fail(__('attribute_groups.attribute_group_with_that_name_exists'));
            }
        }
    }
}
