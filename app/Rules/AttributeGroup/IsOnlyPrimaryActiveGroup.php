<?php

namespace App\Rules\AttributeGroup;

use App\Models\AttributeGroup;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class IsOnlyPrimaryActiveGroup implements ValidationRule, DataAwareRule
{
    protected $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure|\Closure $fail): void
    {
        $isPrimary = request()->is_primary ?? null;
        $isActive = request()->is_active ?? null;
        $attributeGroupId = request()->route('attribute_group')->id ?? null;

        if ($isPrimary && $isActive) {
            $isPrimaryGroupExists = AttributeGroup::where('is_primary', true)->where('is_active', true)->where('id', '!=', $attributeGroupId)->exists();
            if ($isPrimaryGroupExists) {
                $fail(__('attribute_groups.only_one_primary_group_can_be_active'));
            }
        }
    }
}
{
}
