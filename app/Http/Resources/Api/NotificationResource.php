<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use App\Models\Bill;
use App\Models\Notification;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="NotificationResource",
 *     type="object",
 *     title="Notification Resource",
 *     description="Notification resource representation",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="type", type="string", example="waiter"),
 *     @OA\Property(property="description", type="string", example="Table 5 needs assistance"),
 *     @OA\Property(property="readAt", type="string", format="date-time", example="2023-07-30T12:34:56Z"),
 *     @OA\Property(property="table", type="object", ref="#/components/schemas/TableResource"),
 *     @OA\Property(property="bill", type="object", ref="#/components/schemas/BillResource"),
 *     @OA\Property(property="user", type="object", ref="#/components/schemas/UserResource")
 * )
 */
class NotificationResource extends JsonResource
{
    use ResourceTrait;

    /**
     * @var int Default limit items per page
     */
    public const LIMIT = 20;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        $array = [
            'id' => $this->id,
            'type' => Notification::getType($this->type),
            'description' => __($this->data['description']),
            'readAt' => $this->dateFormat($this->read_at),
        ];
        switch (true) {
            case $this->notifiable instanceof Table:
                $array['table'] = new TableResource($this->notifiable);
                break;
            case $this->notifiable instanceof Bill:
                $array['bill'] = new BillResource($this->notifiable);
                if (isset($this->notifiable->orders()->first()->table)) {
                    $array['table'] = new TableResource($this->notifiable->orders()->first()->table);
                }
                break;
            case $this->notifiable instanceof User:
                $array['user'] = new UserResource($this->notifiable);
                $array['table'] = ($this->notifiable->hasRoles([User::ROLE_TABLE, User::ROLE_USER]) && $this->notifiable->table) ? new TableResource($this->notifiable->table) : null;
                break;
        }

        return $array;
    }
}
