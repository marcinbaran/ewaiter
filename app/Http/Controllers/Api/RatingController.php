<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\RatingRequest;
use App\Http\Resources\Api\RatingResource;
use App\Managers\RatingManager;
use App\Models\Rating;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="[MOB] Ratings",
 *     description="[MOB] API Endpoints for managing Ratings"
 * )
 */

class RatingController extends ApiController
{
    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var RatingManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        parent::__construct();
        $this->middleware('can:view,rating', ['only' => ['show']]);
        $this->middleware('can:update,rating', ['only' => ['update']]);
        $this->middleware('can:delate,rating', ['only' => ['destroy']]);
        $this->manager = new RatingManager();
    }

    /**
     * @OA\Get(
     *     path="/api/ratings",
     *     summary="[MOB] Get a list of ratings",
     *     tags={"[MOB] Ratings"},
     *     @OA\Parameter(
     *         name="itemsPerPage",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=20)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Filter by rating ID(s). Can be array or single value.",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"))
     *     ),
     *     @OA\Parameter(
     *         name="noLimit",
     *         in="query",
     *         description="Disable pagination",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of ratings",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/RatingResource")),
     *             @OA\Property(property="locale", type="string", example="en")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function index(RatingRequest $request): AnonymousResourceCollection
    {
        $limit = $request->query->get('itemsPerPage', RatingResource::LIMIT);
        $offset = ($request->query->get('page', 1) - 1) * $limit;
        $order = $request->input('order', ['id' => 'asc']);
        $user = Auth::user();

        $criteria = [
            'id' => (array) $request->id,
            'noLimit' => $request->query->get('noLimit', false),
        ];

        return RatingResource::collection(Rating::getRows($criteria, $order, $limit, $offset));
    }

    /**
     * @OA\Get(
     *     path="/api/ratings/{id}",
     *     summary="[MOB] Get a rating by ID",
     *     tags={"[MOB] Ratings"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Rating ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rating resource",
     *         @OA\JsonContent(ref="#/components/schemas/RatingResource")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Action prohibited.")
     *         )
     *     )
     * )
     */
    public function show(Rating $rating): RatingResource
    {
        return new RatingResource($rating);
    }


    /**
     * @OA\Post(
     *     path="/api/ratings",
     *     summary="[MOB] Create a new rating",
     *     tags={"[MOB] Ratings"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="value", type="integer", example=5),
     *                 @OA\Property(property="comment", type="string", example="Great experience!"),
     *
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rating created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/RatingResource")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function store(RatingRequest $request): RatingResource
    {
        return (new RatingResource($this->manager->create($request)->fresh()))->withStatusCode(201);
    }
    /**
     * @OA\Put(
     *     path="/api/ratings/{id}",
     *     summary="[MOB] Update an existing rating",
     *     tags={"[MOB] Ratings"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Rating ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="value", type="integer", example=4),
     *                 @OA\Property(property="comment", type="string", example="Good experience."),
     *
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rating updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/RatingResource")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Action prohibited.")
     *         )
     *     )
     * )
     */
    public function update(RatingRequest $request, Rating $rating): RatingResource
    {
        return new RatingResource($this->manager->update($request, $rating)->fresh());
    }
    /**
     * @OA\Delete(
     *     path="/api/ratings/{id}",
     *     summary="[MOB] Delete a rating by ID",
     *     tags={"[MOB] Ratings"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Rating ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rating deleted successfully",
     *         @OA\JsonContent(ref="#/components/schemas/RatingResource")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Action prohibited.")
     *         )
     *     )
     * )
     */
    public function destroy(Rating $rating): RatingResource
    {
        return new RatingResource($this->manager->delete($rating));
    }
}
