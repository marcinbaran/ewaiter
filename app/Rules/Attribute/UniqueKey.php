<?php

namespace App\Rules\Attribute;

use App\Models\Attribute;
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
        $attributeId = request()->route('attribute')->id ?? null;
        if ($attributeId) {
            $isAttributeWithTheSameKeyExists = Attribute::where('key', $value)->where('id', '!=', $attributeId)->exists();
            if ($isAttributeWithTheSameKeyExists) {
                $fail(__('attributes.attribute_with_that_name_exists'));
            }
        }
    }
}
