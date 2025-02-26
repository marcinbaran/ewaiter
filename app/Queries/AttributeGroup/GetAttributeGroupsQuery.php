<?php

namespace App\Queries\AttributeGroup;

use App\Http\Requests\Api\AttributeGroupRequest;
use App\Queries\QueryInterface;

class GetAttributeGroupsQuery implements QueryInterface
{
    public function __construct(
        private ?int $id,
    ) {
    }

    public static function createFromRequest(AttributeGroupRequest $request): self
    {
        return new self(
            id: $request->get(AttributeGroupRequest::ID_KEY),
        );
    }

    public function getAttributeGroupId(): ?int
    {
        return $this->id;
    }

    public function isSearchById(): bool
    {
        return $this->id !== null;
    }
}
