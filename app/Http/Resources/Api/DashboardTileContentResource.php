<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="DashboardTileContentResource",
 *     type="object",
 *     title="Dashboard Tile Content Resource",
 *     description="Resource representing the content of a dashboard tile",
 *     @OA\Property(property="id", type="integer", example=1, description="The ID of the dashboard tile content"),
 *     @OA\Property(property="title", type="string", example="Sales Overview", description="The title of the dashboard tile content"),
 *     @OA\Property(property="value", type="string", example="12345", description="The main value displayed in the tile"),
 *     @OA\Property(property="description", type="string", example="Total sales for Q1", description="A brief description of the tile content"),
 *     @OA\Property(property="percentage", type="number", format="float", example=5.5, description="A percentage value, if applicable"),
 *     @OA\Property(property="comparison", type="string", example="+10% from last month", description="Comparison text for the current value with a previous period"),
 *     @OA\Property(property="icon", type="string", example="fa-chart-line", description="Icon representing the tile content")
 * )
 */
class DashboardTileContentResource extends JsonResource
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
