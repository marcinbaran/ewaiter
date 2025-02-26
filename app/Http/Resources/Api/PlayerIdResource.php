<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
/**
 * @OA\Schema(
 *     schema="PlayerIdResource",
 *     type="object",
 *     title="Player ID Resource",
 *     description="Resource representing player ID and associated information",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         example=123,
 *         description="Unique identifier for the player ID entry."
 *     ),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         @OA\Property(
 *             property="id",
 *             type="integer",
 *             example=456,
 *             description="Unique identifier for the user."
 *         ),
 *     ),
 *     @OA\Property(
 *         property="playerId",
 *         type="string",
 *         example="abcdef123456",
 *         description="The player's unique identifier."
 *     ),
 *     @OA\Property(
 *         property="deviceInfo",
 *         type="string",
 *         example="iOS 14.4, iPhone 12",
 *         description="Information about the player's device."
 *     ),
 *     @OA\Property(
 *         property="table",
 *         ref="#/components/schemas/TableResource",
 *         description="Associated table resource, if applicable."
 *     )
 * )
 */
class PlayerIdResource extends JsonResource
{
    use ResourceTrait;

    /**
     * @var int Default limit items per page
     */
    public const LIMIT = 20;

    public function toArray($request)
    {
        $array = [
            'id' => $this->id,
            'user' => [
                'id' => $this->user_id,
            ],
            'playerId' => $this->player_id,
            'deviceInfo' => $this->device_info,
        ];
        if ($this->isWithTable($request)) {
            $array['table'] = new TableResource($this->user->table);
        }

        return $array;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isWithTable(Request $request): bool
    {
        return Auth::user()->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN]) && $this->isPlayerIdsRoute($request);
    }
}
