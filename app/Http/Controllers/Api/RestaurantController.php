<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\RestaurantRequest;
use App\Http\Resources\Api\RestaurantResource;
use App\Managers\RestaurantManager;
use App\Models\Restaurant;
use App\Repositories\MultiTentantRepositoryTrait;
use App\Repositories\RestaurantRepository;
use App\Services\BundleService;
use App\Services\TranslationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="[MOB] Restaurants",
 *     description="[MOB] API Endpoints for managing restaurant resources."
 * )
 */
class RestaurantController extends ApiController
{
    use MultiTentantRepositoryTrait;

    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var RestaurantManager
     */
    private $manager;

    private $bundleService;

    public function __construct(TranslationService $service, BundleService $bundleService)
    {
        parent::__construct();
        $this->bundleService = $bundleService;

        $this->transService = $service;
        $this->manager = new RestaurantManager($this->transService);
    }

    /**
     * @OA\Get(
     *     path="/api/restaurants",
     *     operationId="getAllRestaurants",
     *     tags={"[MOB] Restaurants"},
     *     summary="[MOB] Get collection of restaurants",
     *     description="Retrieve a list of restaurants based on various filters.",
     *     @OA\Parameter(
     *         name="itemsPerPage",
     *         in="query",
     *         description="The number of items per page (max 50)",
     *         required=false,
     *         @OA\Schema(type="integer", default=20)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="The page number of the collection",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="noLimit",
     *         in="query",
     *         description="Disable pagination limits",
     *         required=false,
     *         @OA\Schema(type="boolean", default=false)
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Restaurant ID(s)",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"))
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search filters for restaurants (e.g., search[delivery_address]=1 for restaurants with delivery option)",
     *         required=false,
     *         @OA\Schema(type="object")
     *     ),
     *     @OA\Parameter(
     *         name="visibility",
     *         in="query",
     *         description="Filter by restaurant visibility",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="city",
     *         in="query",
     *         description="Filter by city",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="lat",
     *         in="query",
     *         description="Latitude for geolocation filter",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="lng",
     *         in="query",
     *         description="Longitude for geolocation filter",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="postcode",
     *         in="query",
     *         description="Filter by postal code",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="street",
     *         in="query",
     *         description="Filter by street name and building number",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="withAddress",
     *         in="query",
     *         description="Include address details",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="withRatings",
     *         in="query",
     *         description="Include ratings",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         description="Order results by field",
     *         required=false,
     *         @OA\Schema(
     *             type="object",
     *             @OA\Property(property="id", type="string", enum={"asc", "desc"}),
     *             @OA\Property(property="name", type="string", enum={"asc", "desc"}),
     *             @OA\Property(property="subname", type="string", enum={"asc", "desc"}),
     *             @OA\Property(property="distance", type="string", enum={"asc", "desc"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of restaurants",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Restaurant"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index(RestaurantRequest $request): AnonymousResourceCollection
    {
        $limit = $request->query->get('itemsPerPage', RestaurantResource::LIMIT);
        $offset = ($request->query->get('page', 1) - 1) * $limit;
//        $order = ['id' => 'asc']; // TODO: FIX THIS
        $order = $request->input('order', ['id' => 'asc']);
        $search = $request->input('search', []);
        $locale = $request->query->get('locale', 'pl');
        $names_only = $request->query->get('names_only', 0);

        $criteria = [
            'id' => (array) $request->id,
            'city' => (string) $request->city,
            'postcode' => (string) $request->postcode,
            'street' => (string) $request->street,
            'lat' => (string) $request->lat,
            'lng' => (string) $request->lng,
            'noLimit' => $request->query->get('noLimit', false),
            'visibility' => $request->get('visibility'),
            'locale' => $locale,
        ];

        $filters = [
            'TableBook' => $request->query->get('table_book'),
            'Open' => $request->query->get('open'),
            'News' => $request->query->get('news'),
            'Search' => $request->query->get('s'),
            'RestaurantTags' => $request->query->get('restaurant_tags'),
            'FreeDelivery' => $request->query->get('free_delivery'),
            'Promotions' => $request->query->get('promotions'),
            'Tag' => $request->query->get('tags'),
        ];

        $restaurants = (new RestaurantRepository())->getRestaurantList($criteria, $order, $limit, $offset, $search, $filters, true);
        $response = RestaurantResource::collection($restaurants['restaurants']);

        $response->additional['page'] = intval($request->query->get('page', 1));
        $response->additional['itemsPerPage'] = intval($limit);
        $response->additional['totalPages'] = ceil($restaurants['allRestaurants'] / $limit);


        return $response;
    }

    /**
     * @OA\Get(
     *     path="/api/restaurants/{id}",
     *     operationId="getRestaurantById",
     *     tags={"[MOB] Restaurants"},
     *     summary="[MOB] Get a restaurant resource by ID",
     *     description="Retrieve details of a specific restaurant by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the restaurant",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="withRatings",
     *         in="query",
     *         description="Include ratings",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A restaurant resource",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/Restaurant")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Restaurant not found"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function show($restaurant_id, Request $request): array|JsonResponse
    {
        $restaurant = Restaurant::where('id', $restaurant_id)->orWhere('hostname', $restaurant_id)->first();

        if ($restaurant) {
            $restaurant->locale = $request->query->get('locale', 'pl');

            $data['data'] = new RestaurantResource($restaurant);

            if ($this->isWithBundles($request)) {
                $this->reconnect($restaurant);
                $bundles = $this->bundleService->getBundlesInFakeCategory();
                if (count($bundles['bundles']) > 0) {
                    $data['bundles'] = $bundles;
                }
            }

            return $data;
        }

        return response()->json(['status' => 404, 'error' => 'No restaurant with given ID or hostname'], 404);
    }

    protected function isWithBundles(Request $request): bool
    {
        return (int)$request->withBundles;
    }
}
