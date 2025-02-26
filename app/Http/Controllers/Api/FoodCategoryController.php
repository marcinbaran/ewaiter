<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\FoodCategoryRequest;
use App\Http\Resources\Api\DishResource;
use App\Http\Resources\Api\FoodCategoryResource;
use App\Managers\FoodCategoryManager;
use App\Models\Dish;
use App\Models\FoodCategory;
use App\Services\AllWithDishesService;
use App\Services\BundleService;
use App\Services\TranslationService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Tag(
 *     name="[TENANT] FoodCategory",
 *     description="API for managing food categories"
 * )
 */
class FoodCategoryController extends ApiController
{
    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var FoodCategoryManager
     */
    private $manager;

    private $bundleService;

    public function __construct(TranslationService $service, BundleService $bundleService)
    {
        parent::__construct();
        $this->transService = $service;
        $this->bundleService = $bundleService;
        $this->manager = new FoodCategoryManager($this->transService);
    }

    /**
     * @OA\Get(
     *     path="/api/food-categories",
     *     operationId="getFoodCategories",
     *     tags={"[TENANT] FoodCategory"},
     *     summary="[TENANT] Get list of food categories",
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
     *         name="parent",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         description="Parent category ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/FoodCategoryResource")
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index(FoodCategoryRequest $request): AnonymousResourceCollection
    {
        $limit = $request->query->get('itemsPerPage', FoodCategoryResource::LIMIT);
        $offset = ($request->query->get('page', 1) - 1) * $limit;
        $order = $request->input('order', ['position' => 'asc']);
        $criteria = [
            'id' => (array) $request->id,
            'delivery' => $request->query->get('delivery'),
            'locale' => $request->input('locale', config('app.fallback_locale')),
        ];
        $criteria['parent'] = (int) $request->query->get('parent', 0);
        if ($request->query->has('visibility')) {
            $criteria['visibility'] = (int) $request->query->get('visibility');
        }

        return FoodCategoryResource::collection(FoodCategory::getRows($criteria, $order, $limit, $offset));
    }
    /**
     * @OA\Get(
     *     path="/api/food-categories/all-with-dishes",
     *     operationId="getAllFoodCategoriesWithDishes",
     *     tags={"[TENANT] FoodCategory"},
     *     summary="[TENANT] Get all food categories with dishes",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="food_categories", ref="#/components/schemas/FoodCategoryResource"),
     *             @OA\Property(property="bundles", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function allWithDishes(FoodCategoryRequest $request)
    {
        return app(AllWithDishesService::class)->getAllWithDishes();
    }

    /**
     * @OA\Get(
     *     path="/api/food-categories/{id}",
     *     operationId="getFoodCategoryById",
     *     tags={"[TENANT] FoodCategory"},
     *     summary="[TENANT] Get food category information",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/FoodCategoryResource")
     *     ),
     *     @OA\Response(response=404, description="Food category not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function show(FoodCategory $foodCategory): FoodCategoryResource
    {
        $this->authorize('view', $foodCategory);

        return new FoodCategoryResource($foodCategory);
    }


    /**
     * @OA\Post(
     *     path="/api/food-categories",
     *     operationId="createFoodCategory",
     *     tags={"[TENANT] FoodCategory"},
     *     summary="[TENANT] Create a new food category",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/FoodCategoryRequestPOST")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Food category created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/FoodCategoryResource")
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function store(FoodCategoryRequest $request): FoodCategoryResource
    {
        $this->authorize('create', FoodCategory::class);

        return (new FoodCategoryResource($this->manager->create($request)->fresh()))->withStatusCode(201);
    }


    /**
     * @OA\Put(
     *     path="/api/food-categories/{id}",
     *     operationId="updateFoodCategory",
     *     tags={"[TENANT] FoodCategory"},
     *     summary="[TENANT] Update an existing food category",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/FoodCategoryRequestPut")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Food category updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/FoodCategoryResource")
     *     ),
     *     @OA\Response(response=404, description="Food category not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */

    public function update(FoodCategoryRequest $request, FoodCategory $foodCategory): FoodCategoryResource
    {
        $this->authorize('update', $foodCategory);

        return new FoodCategoryResource($this->manager->update($request, $foodCategory)->fresh());
    }


    /**
     * @OA\Delete(
     *     path="/api/food-categories/{id}",
     *     operationId="deleteFoodCategory",
     *     tags={"[TENANT] FoodCategory"},
     *     summary="[TENANT] Delete a food category",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Food category deleted successfully",
     *         @OA\JsonContent(ref="#/components/schemas/FoodCategoryResource")
     *     ),
     *     @OA\Response(response=404, description="Food category not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function destroy(FoodCategory $foodCategory): FoodCategoryResource
    {
        $this->authorize('delete', $foodCategory);

        return new FoodCategoryResource($this->manager->delete($foodCategory));
    }
}
