<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\PromotionRequest;
use App\Http\Resources\Api\PromotionResource;
use App\Managers\PromotionManager;
use App\Models\Promotion;
use App\Models\User;
use App\Services\TranslationService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
/**
 * @OA\Tag(
 *     name="[TENANT] Promotions",
 *     description="API Endpoints for managing Promotions"
 * )
 */
class PromotionController extends ApiController
{
    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var PromotionManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        parent::__construct();
        $this->transService = $service;
        $this->manager = new PromotionManager($this->transService);
    }



    /**
     * @OA\Get(
     *     path="/api/promotions",
     *     summary="[TENANT] Get a list of promotions",
     *     tags={"[TENANT] Promotions"},
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
     *         description="Filter by promotion ID(s). Can be array or single value.",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"))
     *     ),
     *     @OA\Parameter(
     *         name="orderDish",
     *         in="query",
     *         description="Filter by ordered dish ID(s). Can be array or single value.",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"))
     *     ),
     *     @OA\Parameter(
     *         name="giftDish",
     *         in="query",
     *         description="Filter by gift dish ID(s). Can be array or single value.",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"))
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter by type of promotion",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="typeValue",
     *         in="query",
     *         description="Filter by type value",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="merge",
     *         in="query",
     *         description="Filter by merge status",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="active",
     *         in="query",
     *         description="Filter by active status",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of promotions",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/PromotionResource")),
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
    public function index(PromotionRequest $request): AnonymousResourceCollection
    {
        $limit = $request->query->get('itemsPerPage', PromotionResource::LIMIT);
        $offset = ($request->query->get('page', 1) - 1) * $limit;
        $order = $request->input('order', ['id' => 'asc']);
        $criteria = [
          'id' => (array) $request->id,
          'orderDish' => (array) $request->orderDish,
          'giftDish' => (array) $request->giftDish,
        ];
        ! $request->has('type') ?: $criteria['type'] = $request->type;
        ! $request->has('typeValue') ?: $criteria['typeValue'] = $request->typeValue;
        ! $request->has('marge') ?: $criteria['merge'] = $request->merge;
        $user = Auth::user();
        $user->hasRoles([User::ROLE_TABLE, User::ROLE_USER]) ? $criteria['active'] = Promotion::ACTIVE_YES : (! $request->has('active') ?: $criteria['active'] = $request->active);

        return PromotionResource::collection(Promotion::getRows($criteria, $order, $limit, $offset));
    }



    /**
     * @OA\Get(
     *     path="/api/promotions/{id}",
     *     summary="[TENANT] Get a promotion by ID",
     *     tags={"[TENANT] Promotions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Promotion ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Promotion resource",
     *         @OA\JsonContent(ref="#/components/schemas/PromotionResource")
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
    public function show(Promotion $promotion): PromotionResource
    {
        $this->authorize('view', $promotion);

        return new PromotionResource($promotion);
    }



    /**
     * @OA\Post(
     *     path="/api/promotions",
     *     summary="[TENANT] Create a new promotion",
     *     tags={"[TENANT] Promotions"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="orderDish", type="integer", example=1),
     *                 @OA\Property(property="giftDish", type="integer", example=2),
     *                 @OA\Property(property="type", type="string", example="discount"),
     *                 @OA\Property(property="typeValue", type="string", example="10%"),
     *                 @OA\Property(property="merge", type="string", example="no"),
     *                 @OA\Property(property="active", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Promotion created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/PromotionResource")
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
    public function store(PromotionRequest $request): PromotionResource
    {
        $this->authorize('create', $promotion);
        $promotion = $this->manager->create($request);

        return (new PromotionResource($promotion->fresh()))->withStatusCode(201);
    }



    /**
     * @OA\Put(
     *     path="/api/promotions/{id}",
     *     summary="[TENANT] Update an existing promotion",
     *     tags={"[TENANT] Promotions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Promotion ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="orderDish", type="integer", example=1),
     *                 @OA\Property(property="giftDish", type="integer", example=2),
     *                 @OA\Property(property="type", type="string", example="discount"),
     *                 @OA\Property(property="typeValue", type="string", example="10%"),
     *                 @OA\Property(property="merge", type="string", example="no"),
     *                 @OA\Property(property="active", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Promotion updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/PromotionResource")
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
    public function update(PromotionRequest $request, Promotion $promotion): PromotionResource
    {
        $this->authorize('update', $promotion);

        return new PromotionResource($this->manager->update($request, $promotion)->fresh());
    }



    /**
     * @OA\Delete(
     *     path="/api/promotions/{id}",
     *     summary="[TENANT] Delete a promotion by ID",
     *     tags={"[TENANT] Promotions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Promotion ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Promotion deleted successfully",
     *         @OA\JsonContent(ref="#/components/schemas/PromotionResource")
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
    public function destroy(Promotion $promotion): PromotionResource
    {
        $this->authorize('delate', $promotion);

        return new PromotionResource($this->manager->delete($promotion));
    }
}
