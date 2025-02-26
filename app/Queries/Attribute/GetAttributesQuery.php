<?php

namespace App\Queries\Attribute;

use App\Http\Requests\Api\AttributeRequest;
use App\Queries\QueryInterface;

class GetAttributesQuery implements QueryInterface
{
    public function __construct(
        private ?int $id,
        private ?bool $isWithAttributeGroup,
        ?bool $isActive = null,
    ) {
    }

    public static function createFromRequest(AttributeRequest $request): self
    {
        return new self(
            id: $request->get(AttributeRequest::ID_KEY),
            isWithAttributeGroup: $request->get(AttributeRequest::WITH_ATTRIBUTE_GROUP_KEY),
        );
    }

    public function getAttributeId(): int
    {
        return $this->id;
    }

    public function isSearchById(): bool
    {
        return $this->id !== null;
    }

    public function isWithAttributeGroup(): bool
    {
        return (bool) $this->isWithAttributeGroup;
    }
}
