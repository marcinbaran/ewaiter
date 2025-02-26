<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\DishRequest;
use App\Http\Requests\Api\FilterDishRequest;
use App\Http\Resources\Api\DishResource;
use App\Managers\DishManager;
use App\Models\Dish;
use App\Models\User;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="[TENANT] Dish",
 *     description="API for managing dishes"
 * )
 */
class DishController extends ApiController
{
    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var DishManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        parent::__construct();

        $this->transService = $service;
        $this->manager = new DishManager($this->transService);
    }

    /**
     * @OA\Get(
     *     path="/api/dishes",
     *     operationId="getDishes",
     *     tags={"[TENANT] Dish"},
     *     summary="[TENANT] Get list of dishes",
     *     @OA\Parameter(
     *         name="itemsPerPage",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/DishRequestGet")
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index(DishRequest $request): AnonymousResourceCollection
    {
        $limit = $request->query->get('itemsPerPage', DishResource::LIMIT);
        $offset = ($request->query->get('page', 1) - 1) * $limit;
        // $order = $request->query->get('orderq', ['position' => 'asc']);
        $order = $request->input('order', ['position' => 'asc']);
        $user = Auth::user();

        $criteria = [
            'id' => (array) $request->id,
            'category' => (array) $request->category,
            'noLimit' => $request->query->get('noLimit', false),
            'strictCategory' => $request->query->get('strictCategory', false),
            'onlyWithPromotions' => $request->get('onlyWithPromotions', false),
            'search' => $request->get('s'),
            'locale' => $request->input('locale', config('app.fallback_locale')),
            'attributes' => $request->get('attributes'),
        ];
        if (! $user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN])) {
            $criteria['visibility'] = true;
        } else {
            $criteria['visibility'] = $request->get('visibility');
        }

        if ($request->has('delivery')) {
            $criteria['delivery'] = $request->get('delivery');
        }

        return DishResource::collection(Dish::getRows($criteria, $order, $limit, $offset));
    }

    /**
     * @OA\Get(
     *     path="/api/dishes/{id}",
     *     operationId="getDishById",
     *     tags={"[TENANT] Dish"},
     *     summary="[TENANT] Get dish information",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/DishRequestGet")
     *     ),
     *     @OA\Response(response=404, description="Dish not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function show(Dish $dish): DishResource
    {
        $this->authorize('view', $dish);

        return new DishResource($dish);
    }

    /**
     * @OA\Post(
     *     path="/api/dishes",
     *     operationId="createDish",
     *     tags={"[TENANT] Dish"},
     *     summary="[TENANT] Create a new dish",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/DishRequestPost")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Dish created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/DishRequestPost")
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */

    public function store(DishRequest $request): DishResource
    {
        $this->authorize('create');

        return (new DishResource($this->manager->createFromRequest($request)->fresh()))->withStatusCode(201);
    }

    /**
     * @OA\Put(
     *     path="/api/dishes/{id}",
     *     operationId="updateDish",
     *     tags={"[TENANT] Dish"},
     *     summary="[TENANT] Update an existing dish",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/DishRequestPut")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dish updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/DishRequestPut")
     *     ),
     *     @OA\Response(response=404, description="Dish not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function update(DishRequest $request, Dish $dish): DishResource
    {
        $this->authorize('update', $dish);

        return new DishResource($this->manager->updateFromRequest($request, $dish)->fresh());
    }

    /**
     * @OA\Delete(
     *     path="/api/dishes/{id}",
     *     operationId="deleteDish",
     *     tags={"[TENANT] Dish"},
     *     summary="[TENANT] Delete a dish",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dish deleted successfully",
     *         @OA\JsonContent(ref="#/components/schemas/DishRequestDelete")
     *     ),
     *     @OA\Response(response=404, description="Dish not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function destroy(Dish $dish): DishResource
    {
        $this->authorize('delete', $dish);

        return new DishResource($this->manager->delete($dish));
    }
    /**
     * @OA\Get(
     *     path="/api/filter_dishes",
     *     operationId="filterDishes",
     *     tags={"[TENANT] Dish"},
     *     summary="[TENANT] Filter dishes based on criteria",
     *     @OA\Parameter(
     *         name="itemsPerPage",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/DishRequestGet")
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function filter(FilterDishRequest $request) // TODO: THIS IS HOTFIX AND NEEDS TO BE REFACTORED
    {
        $limit = $request->query->get('itemsPerPage', DishResource::LIMIT);
        $offset = ($request->query->get('page', 1) - 1) * $limit;
        $order = $request->input('order', ['position' => 'asc']);
        $user = Auth::user();

        $criteria = [
            'id' => (array) $request->id,
            'category' => (array) $request->category,
            'noLimit' => $request->query->get('noLimit', false),
            'strictCategory' => $request->query->get('strictCategory', false),
            'onlyWithPromotions' => $request->get('onlyWithPromotions', false),
            'locale' => $request->input('locale', config('app.fallback_locale')),
            'attribute_filters' => $request->get('attribute_filters'),
            'price_range' => $request->get('price_range'),
        ];
        if (! $user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN])) {
            $criteria['visibility'] = true;
        } else {
            $criteria['visibility'] = $request->get('visibility');
        }

        if ($request->has('delivery')) {
            $criteria['delivery'] = $request->get('delivery');
        }

        return DishResource::collection(Dish::getRows($criteria, $order, $limit, $offset));
    }
}
