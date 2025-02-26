<?php

namespace App\Commands\AttributeGroup;

use App\Commands\CommandInterface;
use App\Http\Requests\Api\AttributeGroupRequest;

class DeleteAttributeGroupCommand implements CommandInterface
{
    public function __construct(
        private int $id,
    ) {
    }

    public static function createFromRequest(AttributeGroupRequest $request): self
    {
        return new self(
            id: $request->get(AttributeGroupRequest::ID_KEY),
        );
    }

    public function getAttributeGroupId(): int
    {
        return $this->id;
    }
}
