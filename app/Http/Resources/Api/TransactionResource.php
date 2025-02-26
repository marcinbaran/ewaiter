<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="Transaction",
 *     type="object",
 *     title="Transaction",
 *     description="Transaction details"
 * )
 */
class TransactionResource extends JsonResource
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

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     *
     * @OA\Property(
     *     property="id",
     *     type="integer",
     *     description="The unique identifier of the transaction"
     * )
     * @OA\Property(
     *     property="tag",
     *     type="string",
     *     description="The tag associated with the transaction"
     * )
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     description="The translated name of the transaction"
     * )
     * @OA\Property(
     *     property="icon",
     *     type="string",
     *     description="The icon associated with the transaction"
     * )
     * @OA\Property(
     *     property="description",
     *     type="string",
     *     description="The translated description of the transaction"
     * )
     * @OA\Property(
     *     property="visibility",
     *     type="boolean",
     *     description="Indicates if the transaction is visible"
     * )
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'tag' => $this->tag,
            'name' => gtrans('tags.'.$this->name),
            'icon' => $this->icon,
            'description' => gtrans('tags.'.$this->description),
            'visibility' => $this->visibility,
        ];
    }
}
