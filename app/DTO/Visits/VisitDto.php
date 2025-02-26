<?php

namespace App\DTO\Visits;

use App\Enum\VisitObject;
use App\Http\Requests\Api\SaveVisitRequest;
use App\Models\Restaurant;

final class VisitDto
{
    public function __construct(
        private string $ipAddress,
        private ?string $macAddress,
        private string $restaurantName,
        private int $restaurantId,
        private int $userId,
        private ?VisitObject $visitObjectType,
        private ?string $visitObjectName,
        private ?int $visitObjectId,
    ) {
    }

    public static function createFromRequest(SaveVisitRequest $request): self
    {
        $currentRestaurant = Restaurant::getCurrentRestaurant();

        return new self(
            ipAddress: $request->ip(),
            macAddress: $request->get(SaveVisitRequest::MAC_ADDRESS_PARAM_KEY),
            restaurantName: $currentRestaurant->name,
            restaurantId: $currentRestaurant->id,
            userId: auth()->id(),
            visitObjectType: VisitObject::from($request->get(SaveVisitRequest::VISIT_OBJECT_TYPE_PARAM_KEY)),
            visitObjectName: $request->get(SaveVisitRequest::VISIT_OBJECT_NAME_PARAM_KEY),
            visitObjectId: $request->get(SaveVisitRequest::VISIT_OBJECT_ID_PARAM_KEY),
        );
    }

    public function toArray(): array
    {
        return [
            'ip_address' => $this->ipAddress,
            'mac_address' => $this->macAddress,
            'restaurant_name' => $this->restaurantName,
            'restaurant_id' => $this->restaurantId,
            'user_id' => $this->userId,
            'visit_object_type' => $this->visitObjectType->value,
            'visit_object_name' => $this->visitObjectName,
            'visit_object_id' => $this->visitObjectId,
        ];
    }
}
