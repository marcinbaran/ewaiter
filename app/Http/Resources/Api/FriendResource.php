<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="FriendResource",
 *     type="object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID of the friend resource"
 *     ),
 *     @OA\Property(
 *         property="senderId",
 *         type="integer",
 *         description="ID of the sender"
 *     ),
 *     @OA\Property(
 *         property="senderName",
 *         type="string",
 *         description="Name of the sender"
 *     ),
 *     @OA\Property(
 *         property="receiverId",
 *         type="integer",
 *         description="ID of the receiver"
 *     ),
 *     @OA\Property(
 *         property="receiverName",
 *         type="string",
 *         description="Name of the receiver"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="integer",
 *         description="Status of the friend request"
 *     ),
 *     @OA\Property(
 *         property="updatedAt",
 *         type="string",
 *         format="date",
 *         description="Date when the friend request was last updated"
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         description="Phone number of the friend"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         description="Email of the friend"
 *     ),
 *     @OA\Property(
 *         property="friend_invite_method",
 *         type="string",
 *         description="Method by which the friend invite was sent"
 *     )
 * )
 */
class FriendResource extends JsonResource
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
        $current_user = auth()->user()->id;
        $array = [
            'id' => $this->id,
            'senderId' => $this->sender_id,
            'senderName' => $this->sender_name,
            'receiverId' => $this->receiver_id,
            'receiverName' => $this->receiver_name,
            'status' => $this->status,
            'updatedAt' => $this->updated_at->toDateString(),
            'phone' => $this['friend_data']->phone ?? $this->phone,
            'email' => $this['friend_data']->email ?? $this->email,
            'friend_invite_method' => $this->friend_invite_method,
        ];

        return $array;
    }
}
