<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="Worktime",
 *     type="object",
 *     title="Worktime",
 *     description="Details of worktime schedule"
 * )
 */
class WorktimeResource extends JsonResource
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
     *
     * @OA\Property(property="id", type="integer", description="The unique identifier of the worktime entry"),
     * @OA\Property(property="start", type="string", format="date-time", description="The start time of the worktime"),
     * @OA\Property(property="end", type="string", format="date-time", description="The end time of the worktime"),
     * @OA\Property(property="type", type="string", description="The type of worktime (e.g., shift, break)"),
     * @OA\Property(property="visibility", type="boolean", description="Indicates if the worktime is visible"),
     */

    public function toArray($request): array
    {
        $array = [
            'id' => $this->id,
            'start' => $this->start,
            'end' => $this->end,
            'type' => $this->type,
            'visibility' => $this->visibility,
        ];

        return $array;
    }
}
