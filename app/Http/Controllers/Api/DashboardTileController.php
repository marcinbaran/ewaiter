<?php

namespace App\Http\Controllers\Api;

//use App\Http\Resources\Api\VersionResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\DashboardTileResource;
use App\Models\DashboardTile;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
/**
 * @OA\Tag(
 *     name="[MOB] Dashboard Tiles",
 *     description="[MOB] API Endpoints of Dashboard Tiles"
 * )
 */
class DashboardTileController extends Controller
{
    //
    /**
     * @OA\Get(
     *     path="/api/dashboard-tiles",
     *     summary="[MOB] Get a list of dashboard tiles",
     *     description="[MOB] Returns a list of dashboard tiles with their details",
     *     tags={"[MOB] Dashboard Tiles"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/DashboardTile")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Forbidden.")
     *         )
     *     )
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        return DashboardTileResource::collection(DashboardTile::getRows());
    }
}
