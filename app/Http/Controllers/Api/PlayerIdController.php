<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\PlayerIdRequest;
use App\Http\Resources\Api\PlayerIdResource;
use App\Managers\PlayerIdManager;
use App\Models\PlayerId;
use App\Models\User;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
/**
 * @OA\Tag(
 *     name="[TENANT] PlayerIds",
 *     description="API Endpoints for managing Player IDs"
 * )
 */
class PlayerIdController extends ApiController
{
    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var PlayerIdManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        parent::__construct();
        $this->middleware('can:view,App\\User,App\\PlayerId', ['only' => ['index']]);
        $this->middleware('can:view,playerId', ['only' => ['show']]);
        $this->middleware('can:create,App\\PlayerId', ['only' => ['store']]);
        $this->middleware('can:update,playerId', ['only' => ['update']]);
        $this->middleware('can:delate,playerId', ['only' => ['destroy']]);
        $this->transService = $service;
        $this->manager = new PlayerIdManager($this->transService);
    }


    /**
     * @OA\Get(
     *     path="/api/player_ids",
     *     summary="[TENANT] Get a list of player IDs",
     *     tags={"[TENANT] PlayerIds"},
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
     *         description="Filter by Player ID(s). Can be array or single value.",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"))
     *     ),
     *     @OA\Parameter(
     *         name="user",
     *         in="query",
     *         description="Filter by User ID(s). Can be array or single value.",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"))
     *     ),
     *     @OA\Parameter(
     *         name="table",
     *         in="query",
     *         description="Filter by Table ID(s). Can be array or single value.",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of player IDs",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/PlayerIdResource")),
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
    public function index(PlayerIdRequest $request): AnonymousResourceCollection
    {
        $limit = $request->query->get('itemsPerPage', PlayerIdResource::LIMIT);
        $offset = ($request->query->get('page', 1) - 1) * $limit;
        $order = $request->input('order', ['id' => 'asc']);
        $criteria = ['id' => (array) $request->id, 'user' => (array) $request->user, 'table' => (array) $request->table];
        $user = Auth::user();
        $criteria['user'] = (array) ($user->hasRoles([User::ROLE_TABLE, User::ROLE_USER]) ? [$user->id] : $request->user);

        return PlayerIdResource::collection(PlayerId::getRows($criteria, $order, $limit, $offset));
    }



    /**
     * @OA\Get(
     *     path="/api/player_ids/{id}",
     *     summary="[TENANT] Get a player ID by ID",
     *     tags={"[TENANT] PlayerIds"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Player ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Player ID resource",
     *         @OA\JsonContent(ref="#/components/schemas/PlayerIdResource")
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
    public function show(PlayerId $playerId): PlayerIdResource
    {
        return new PlayerIdResource($playerId);
    }



    /**
     * @OA\Post(
     *     path="/api/player_ids",
     *     summary="[TENANT] Create a new player ID",
     *     tags={"[TENANT] PlayerIds"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="user", type="integer", example=1),
     *                 @OA\Property(property="table", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Player ID created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/PlayerIdResource")
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
    public function store(PlayerIdRequest $request): PlayerIdResource
    {
        return (new PlayerIdResource($this->manager->create($request)->fresh()))->withStatusCode(201);
    }



    /**
     * @OA\Put(
     *     path="/api/player_ids/{id}",
     *     summary="[TENANT] Update an existing player ID",
     *     tags={"[TENANT] PlayerIds"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Player ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="user", type="integer", example=1),
     *                 @OA\Property(property="table", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Player ID updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/PlayerIdResource")
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
    public function update(PlayerIdRequest $request, PlayerId $playerId): PlayerIdResource
    {
        return new PlayerIdResource($this->manager->update($request, $playerId)->fresh());
    }



    /**
     * @OA\Delete(
     *     path="/api/player_ids/{id}",
     *     summary="[TENANT] Delete a player ID by ID",
     *     tags={"[TENANT] PlayerIds"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Player ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Player ID deleted successfully",
     *         @OA\JsonContent(ref="#/components/schemas/PlayerIdResource")
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
    public function destroy(PlayerId $playerId): PlayerIdResource
    {
        return new PlayerIdResource($this->manager->delete($playerId));
    }
}
