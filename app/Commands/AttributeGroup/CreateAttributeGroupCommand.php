<?php

namespace App\Commands\AttributeGroup;

use App\Commands\CommandInterface;
use App\Http\Requests\Api\AttributeGroupRequest;

class CreateAttributeGroupCommand implements CommandInterface
{
    public function __construct(
        private array $name,
        private string $inputType,
        private bool $isPrimary,
        private bool $isActive,
    ) {
    }

    public static function createFromRequest(AttributeGroupRequest $request): self
    {
        return new self(
            name: $request->get(AttributeGroupRequest::NAME_KEY),
            inputType: $request->get(AttributeGroupRequest::INPUT_TYPE_KEY),
            isPrimary: $request->get(AttributeGroupRequest::IS_PRIMARY_KEY),
            isActive: $request->get(AttributeGroupRequest::IS_ACTIVE_KEY),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'input_type' => $this->inputType,
            'is_primary' => $this->isPrimary,
            'is_active' => $this->isActive,
        ];
    }
}
