<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Settings",
 *     type="object",
 *     title="Settings",
 *     description="Settings details"
 * )
 */
class SettingsResource extends JsonResource
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
     *     description="The unique identifier of the setting"
     * )
     * @OA\Property(
     *     property="key",
     *     type="string",
     *     description="The key identifier of the setting"
     * )
     * @OA\Property(
     *     property="value",
     *     type="string",
     *     description="The value associated with the setting"
     * )
     * @OA\Property(
     *     property="value_type",
     *     type="string",
     *     description="The type of the value (e.g., string, integer)"
     * )
     * @OA\Property(
     *     property="value_active",
     *     type="boolean",
     *     description="Indicates if the setting value is active"
     * )
     * @OA\Property(
     *     property="description",
     *     type="string",
     *     description="Description of the setting"
     * )
     */
    public function toArray($request): array
    {
        return parent::toArray($request);
    }
}
