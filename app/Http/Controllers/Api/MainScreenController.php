<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiExceptions\Delivery\DeliveryAddressExceedsTheRestaurantRange;
use App\Exceptions\ApiExceptions\Delivery\DeliveryOptionIsDisabledException;
use App\Exceptions\ApiExceptions\Delivery\ItemsCannotBeDeliveredException;
use App\Exceptions\ApiExceptions\Delivery\NoValidDeliveryTypeException;
use App\Exceptions\ApiExceptions\General\ProhibitedActionException;
use App\Exceptions\ApiExceptions\Order\AvailabilityOfProductsChangedException;
use App\Exceptions\ApiExceptions\Order\MandatoryAdditionsNotSelectedException;
use App\Exceptions\ApiExceptions\Order\MinimumOrderValueNotExceededException;
use App\Exceptions\ApiExceptions\Payment\PaymentTypeNotAvailable;
use App\Exceptions\ApiExceptions\Payment\UserInactiveChooseOtherPaymentMethodException;
use App\Exceptions\ApiExceptions\Points\AvailabilityOfPointsChangedException;
use App\Exceptions\ApiExceptions\Restaurant\RestaurantIsClosedException;
use App\Http\Requests\Api\BillRequest;
use App\Http\Resources\Api\BillResource;
use App\Http\Resources\Api\RestaurantResource;
use App\Managers\BillManager;
use App\Models\Bill;
use App\Repositories\RestaurantRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="[MOB] MainScreen",
 *     description="API for main screen restaurant lists"
 * )
 */
class MainScreenController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/api/mainscreen/latest",
     *     tags={"[MOB] MainScreen"},
     *     summary="[MOB] Get the latest restaurants",
     *     description="Returns a list of the latest restaurants based on their creation date.",
     *     @OA\Parameter(
     *         name="locale",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Locale for the restaurant details"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/RestaurantResource"))
     *     )
     * )
     */
    public function latest(BillRequest $request): AnonymousResourceCollection
    {
        $limit = 5;
        $order = ['created_at' => 'desc'];

        return $this->fetchRestaurants($request, $order, $limit);
    }
    /**
     * @OA\Get(
     *     path="/api/mainscreen/closest",
     *     tags={"[MOB] MainScreen"},
     *     summary="[MOB] Get the closest restaurants",
     *     description="Returns a list of the closest restaurants based on the user's location.",
     *     @OA\Parameter(
     *         name="lat",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Latitude of the user's location"
     *     ),
     *     @OA\Parameter(
     *         name="lng",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Longitude of the user's location"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/RestaurantResource"))
     *     )
     * )
     */
    public function closest(BillRequest $request): AnonymousResourceCollection
    {
        $limit = 5;
        $order = ['distance' => 'desc'];

        return $this->fetchRestaurants($request, $order, $limit);
    }
    /**
     * @OA\Get(
     *     path="/api/mainscreen/most-popular",
     *     tags={"[MOB] MainScreen"},
     *     summary="[MOB] Get the most popular restaurants",
     *     description="Returns a list of the most popular restaurants based on the number of orders.",
     *     @OA\Parameter(
     *         name="locale",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Locale for the restaurant details"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/RestaurantResource"))
     *     )
     * )
     */
    public function mostPopular(BillRequest $request): AnonymousResourceCollection
    {
        $limit = 5;
        $order = ['orders_count' => 'desc'];

        return $this->fetchRestaurants($request, $order, $limit);
    }
    /**
     * Prepare criteria for fetching restaurants based on request parameters.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    private function getCriteriaFromRequest($request)
    {
        $locale = $request->query->get('locale', 'pl');

        return [
            'id' => (array) $request->id,
            'city' => (string) $request->city,
            'postcode' => (string) $request->postcode,
            'street' => (string) $request->street,
            'lat' => (string) $request->lat,
            'lng' => (string) $request->lng,
            'noLimit' => $request->query->get('noLimit', false),
            'locale' => $locale,
        ];
    }
    /**
     * Populate the request with default parameters for fetching restaurants.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    private function hydrateRequest($request)
    {
        request()->withAddress = 1;
        request()->visibility = 1;
        request()->withDelivery = 1;
        request()->with_attributes = 1;
        request()->withoutMenu = 1;
        request()->with_labels = 1;
        request()->withReviews = 1;

    }

    /**
     * Fetch restaurants based on criteria, order, and limit.
     *
     * @param \App\Http\Requests\Api\BillRequest $request
     * @param array $order
     * @param int $limit
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    private function fetchRestaurants(BillRequest $request, array $order, int $limit): AnonymousResourceCollection
    {
        $this->hydrateRequest($request);

        $restaurants = (new RestaurantRepository())
            ->getRestaurantList($this->getCriteriaFromRequest($request), $order, 5, 0, [], [], true);

        $response = RestaurantResource::collection($restaurants['restaurants']);

        $response->additional['page']         = intval($request->query->get('page', 1));
        $response->additional['itemsPerPage'] = intval($limit);
        $response->additional['totalPages']   = ceil($restaurants['allRestaurants'] / $limit);

        return $response;
    }

}
