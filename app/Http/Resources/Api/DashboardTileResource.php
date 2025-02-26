<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="DashboardTile",
 *     type="object",
 *     title="Dashboard Tile",
 *     description="Dashboard Tile resource",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="The ID of the dashboard tile",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the dashboard tile",
 *         example="Sales Statistics"
 *     ),
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             description="Data related to the dashboard tile",
 *             example={"revenue": 10000, "growth": "10%"}
 *         ),
 *         description="The data displayed on the dashboard tile"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="The creation date of the dashboard tile",
 *         example="2024-07-30T12:34:56Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="The last update date of the dashboard tile",
 *         example="2024-07-30T12:34:56Z"
 *     )
 * )
 */
class DashboardTileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
