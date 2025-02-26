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
use App\Managers\BillManager;
use App\Models\Bill;
use App\Services\TranslationService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="[TENANT] Bills",
 *     description="[TENANT] API Endpoints of Bills"
 * )
 */
class BillController extends ApiController
{
    private $transService;

    private $manager;

    public function __construct(TranslationService $service)
    {
        parent::__construct();
        $this->transService = $service;
        $this->manager = new BillManager($this->transService);
    }
    /**
     * @OA\Get(
     *     path="/bills",
     *     summary="[TENANT] [MOB] List all bills",
     *     tags={"[TENANT] Bills"},
     *     @OA\Parameter(
     *         name="itemsPerPage",
     *         in="query",
     *         required=false,
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="noLimit",
     *         in="query",
     *         required=false,
     *         description="No limit on results",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="orderBy",
     *         in="query",
     *         required=false,
     *         description="Field to sort by",
     *         @OA\Schema(type="string", example="id")
     *     ),
     *     @OA\Parameter(
     *         name="orderDirection",
     *         in="query",
     *         required=false,
     *         description="Sort direction",
     *         @OA\Schema(type="string", example="desc")
     *     ),
     *     @OA\Parameter(
     *         name="withOrders",
     *         in="query",
     *         required=false,
     *         description="Include orders",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of bills",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/BillResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function index(BillRequest $request): AnonymousResourceCollection
    {
        $limit = $request->query->get('itemsPerPage', BillResource::LIMIT);
        $offset = ($request->query->get('page', 1) - 1) * $limit;
        $orderBy = $request->query('orderBy', 'id');
        $orderDirection = $request->query('orderDirection', 'desc');
        $order = $request->input('order', [$orderBy => $orderDirection]);


        $user = Auth::user();
        $criteria = [
            'id' => (array)$request->id,
            'status' => (array)$request->status,
            'noLimit' => $request->query->get('noLimit', false),
            'withCarts' => $request->query->get('withCarts', false),
            'paidType' => (array)$request->paidType,
            'roomDelivery' => (array)$request->roomDelivery,
            'onlyWithGuestBills' => $user->guest ? $user->id : false,
            'fromDate' => $request->fromDate,
            'toDate' => $request->toDate,
            'date' => $request->date,
        ];
        !$request->has('paid') ?: $criteria['paid'] = $request->paid;
        $criteria['user'] = (array)($user->isEndUserRole() ? [$user->id] : $request->user);
        $criteria['withOrders'] = (int)($user->isEndUserRole() && !$request->query->has('withOrders') ? true : $request->withOrders);

        $billresource = BillResource::collection(Bill::getRestaurantsRows($criteria, $order, $limit, $offset));

        $billresource->additional['page'] = $request->query->get('page', 1);
        $billresource->additional['itemsPerPage'] = $limit;
        $criteria['noLimit'] = true;
        $billresource->additional['totalPages'] = ceil(Bill::getRestaurantsRows($criteria, $order, $limit, $offset)->count() / $limit);
        $billresource->additional['orderBy'] = $orderBy;
        $billresource->additional['orderDirection'] = $orderDirection;

        return $billresource;
    }
    /**
     * @OA\Get(
     *     path="/bills/all",
     *     summary="[TENANT] List all bills (including more data)",
     *     tags={"[TENANT] Bills"},
     *     @OA\Parameter(
     *         name="itemsPerPage",
     *         in="query",
     *         required=false,
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="orderBy",
     *         in="query",
     *         required=false,
     *         description="Field to sort by",
     *         @OA\Schema(type="string", example="id")
     *     ),
     *     @OA\Parameter(
     *         name="orderDirection",
     *         in="query",
     *         required=false,
     *         description="Sort direction",
     *         @OA\Schema(type="string", example="desc")
     *     ),
     *     @OA\Parameter(
     *         name="withOrders",
     *         in="query",
     *         required=false,
     *         description="Include orders",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of all bills",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/BillResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function index_all(BillRequest $request): AnonymousResourceCollection
    {
        $limit = $request->query->get('itemsPerPage', BillResource::LIMIT);
        $offset = ($request->query->get('page', 1) - 1) * $limit;
        $orderBy = $request->query('orderBy', 'id');
        $orderDirection = $request->query('orderDirection', 'desc');
        $order = $request->input('order', [$orderBy => $orderDirection]);

        $user = Auth::user();
        $criteria = [
            'id' => (array)$request->id,
            'status' => (array)$request->status,
            'restaurant_id' => (array)$request->restaurant_id,
            'noLimit' => $request->query->get('noLimit', false),
            'withCarts' => $request->query->get('withCarts', false),
            'paidType' => (array)$request->paidType,
            'roomDelivery' => (array)$request->roomDelivery,
            'onlyWithGuestBills' => $user->guest ? $user->id : false,
            'fromDate' => $request->fromDate,
            'toDate' => $request->toDate,
            'date' => $request->date,
        ];
        !$request->has('paid') ?: $criteria['paid'] = $request->paid;
        $criteria['user'] = (array)($user->isEndUserRole() ? [$user->id] : $user->id);
        //$criteria['user'] = (array) ($user->isEndUserRole() ? [$user->id] : (array) $request->user);
        $criteria['withOrders'] = (int)($user->isEndUserRole() && !$request->query->has('withOrders') ? true : $request->withOrders);

        $billresource = BillResource::collection(Bill::getRestaurantsRows($criteria, $order, $limit, $offset));

        $billresource->additional['page'] = $request->query->get('page', 1);
        $billresource->additional['itemsPerPage'] = $limit;
        $criteria['noLimit'] = true;
        $billresource->additional['totalPages'] = ceil(Bill::getRestaurantsRows($criteria, $order, $limit, $offset)->count() / $limit);
        $billresource->additional['orderBy'] = $orderBy;
        $billresource->additional['orderDirection'] = $orderDirection;

        return $billresource;
    }
    /**
     * @OA\Get(
     *     path="/bills/{id}",
     *     summary="[TENANT] Get a specific bill",
     *     tags={"[TENANT][MOB] Bills"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the bill",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Details of the bill",
     *         @OA\JsonContent(ref="#/components/schemas/BillResource")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bill not found"
     *     )
     * )
     */
    public function show(Bill $bill): BillResource
    {
        $this->authorize('view', $bill);

        $user_logged = Auth::user();
        if ($user_logged->isEndUserRole() && $user_logged->id != $bill->user_id) {
            throw new ProhibitedActionException();
        }

        return new BillResource($bill);
    }

    /**
     * @OA\Post(
     *     path="/bills",
     *     summary="[TENANT] Create a new bill",
     *     tags={"[TENANT] Bills"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/BillRequest_POST")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Bill created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/BillResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function store(BillRequest $request): BillResource
    {
        $this->authorize('create', Bill::class);

        if (!$this->manager->checkAdditions($request)) {
            throw new MandatoryAdditionsNotSelectedException();
        }

        if (!$this->manager->checkDeliveryOption($request)) {
            throw new DeliveryOptionIsDisabledException();
        }

        if (!$this->manager->checkIfOpen()) {
            throw new RestaurantIsClosedException();
        }

        if (!auth()->user()->guest && $this->manager->checkNotActivatedAccount($request)) {
            throw new UserInactiveChooseOtherPaymentMethodException();
        }

        if ($this->manager->checkPaymentOption($request)) {
            throw new PaymentTypeNotAvailable(['payment' => 'Payment type hotelBill is only available for room delivery']);
        }

        if ($this->manager->checkDeliveryType($request)) {
            throw new NoValidDeliveryTypeException();
        }

        if (!($this->manager->checkDeliveryAvailability($request))) {
            throw new ItemsCannotBeDeliveredException();
        }

        if ($request->address || $request->deliveryType['type'] == 'delivery_address') {
            $is_range_ok = $this->manager->checkDeliveryRange($request);

            if (!$is_range_ok) {
                throw new DeliveryAddressExceedsTheRestaurantRange();
            }
        }

        $bill = $this->manager->create($request)->fresh();

        if ($bill->address) {
            if (!$this->manager->checkDeliveryMinimumPrice($bill)) {
                $this->manager->delete($bill);

                throw new MinimumOrderValueNotExceededException();
            }
        }

        return (new BillResource($bill))->withStatusCode(201);
    }
    /**
     * @OA\Post(
     *     path="/bills/quick-order",
     *     summary="[TENANT] Create a quick order bill",
     *     tags={"[TENANT] Bills"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/BillRequest_POST")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Quick order bill created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/BillResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function quick_order(BillRequest $request): BillResource
    {
        $this->authorize('create', Bill::class);

        if ($this->manager->checkDeliveryOption($request)) {
            throw new DeliveryOptionIsDisabledException();
        }

        if (!$this->manager->checkIfOpen()) {
            throw new RestaurantIsClosedException();
        }

        if (!auth()->user()->guest && $this->manager->checkNotActivatedAccount($request)) {
            throw new UserInactiveChooseOtherPaymentMethodException();
        }

        if (!$this->manager->checkPoints($request, $bill)) {
            throw new AvailabilityOfPointsChangedException();
        }

        if ($this->manager->checkProductStatuses($bill)) {
            throw new AvailabilityOfProductsChangedException();
        }

        if ($request->address) {
            $is_range_ok = $this->manager->checkDeliveryRange($request);

            if (!$is_range_ok) {
                throw new DeliveryAddressExceedsTheRestaurantRange();
            }
        }

        $bill = $this->manager->create($request)->fresh();

        if ($request->address) {
            if (!$this->manager->checkDeliveryMinimumPrice($bill)) {
                $this->manager->delete($bill);
                throw new MinimumOrderValueNotExceededException();
            }
        }

        return (new BillResource($bill))->withStatusCode(201);
    }
    /**
     * @OA\Put(
     *     path="/bills/{id}",
     *     summary="[TENANT] Update an existing bill",
     *     tags={"[TENANT] Bills"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the bill",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/BillRequest_PUT")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bill updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/BillResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function update(BillRequest $request, Bill $bill): BillResource
    {
        $this->authorize('update', $bill);

        if (!$this->manager->checkIfOpen()) {
            throw new RestaurantIsClosedException();
        }

        $user_logged = Auth::user();

        if ($user_logged->isEndUserRole() && $user_logged->id != $bill->user_id) {
            throw new ProhibitedActionException();
        }

        if (!$this->manager->checkPoints($request, $bill)) {
            throw new AvailabilityOfPointsChangedException();
        }

        if ($this->manager->checkProductStatuses($bill)) {
            throw new AvailabilityOfProductsChangedException();
        }

        return new BillResource($this->manager->update($request, $bill)->fresh());
    }
    /**
     * @OA\Delete(
     *     path="/bills/{id}",
     *     summary="[TENANT] Delete a specific bill",
     *     tags={"[TENANT] Bills"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the bill",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bill deleted successfully",
     *         @OA\JsonContent(ref="#/components/schemas/BillResource")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bill not found"
     *     )
     * )
     */
    public function destroy(Bill $bill): BillResource
    {
        $this->authorize('delate', $bill);

        $user_logged = Auth::user();

        if ($user_logged->isEndUserRole() && $user_logged->id != $bill->user_id) {
            throw new ProhibitedActionException();
        }

        return new BillResource($this->manager->delete($bill));
    }
}
