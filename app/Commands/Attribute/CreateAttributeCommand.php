<?php

namespace App\Commands\Attribute;

use App\Commands\CommandInterface;
use App\Http\Requests\Api\AttributeRequest;

final class CreateAttributeCommand implements CommandInterface
{
    public function __construct(
        private array $name,
        private ?string $icon,
        private bool $isActive,
        private ?int $attributeGroupId,
    ) {
    }

    public static function createFromRequest(AttributeRequest $request): self
    {
        return new self(
            name: $request->get(AttributeRequest::NAME_KEY),
            icon: $request->get(AttributeRequest::ICON_KEY),
            isActive: $request->get(AttributeRequest::IS_ACTIVE_KEY),
            attributeGroupId: $request->get(AttributeRequest::ATTRIBUTE_GROUP_ID_KEY),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'icon' => $this->icon,
            'is_active' => $this->isActive,
            'attribute_group_id' => $this->attributeGroupId,
        ];
    }
}
