<?php

namespace App\Commands\Attribute;

use App\Commands\CommandInterface;
use App\Http\Requests\Api\AttributeRequest;

class DeleteAttributeCommand implements CommandInterface
{
    public function __construct(
        private int $id,
    ) {
    }

    public static function createFromRequest(AttributeRequest $request): self
    {
        return new self(
            id: $request->get(AttributeRequest::ID_KEY),
        );
    }

    public function getAttributeId(): int
    {
        return $this->id;
    }
}
