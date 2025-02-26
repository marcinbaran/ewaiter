<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="VersionResource",
 *     type="object",
 *     title="Version",
 *     description="Application version details"
 * )
 */
class VersionResource extends JsonResource
{
    use ResourceTrait;


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     *
     * @OA\Property(property="id", type="integer", description="The unique identifier of the version"),
     * @OA\Property(property="version_number", type="string", description="The version number of the application"),
     * @OA\Property(property="release_notes", type="string", description="Release notes for the version"),
     * @OA\Property(property="created_at", type="string", format="date-time", description="The date and time when the version was created"),
     * @OA\Property(property="updated_at", type="string", format="date-time", description="The date and time when the version was last updated")
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
