<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\OrderRequest;
use App\Http\Resources\Api\OrderResource;
use App\Managers\OrderManager;
use App\Models\Order;
use App\Models\User;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Kalnoy\Nestedset\Collection;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @OA\Tag(
 *     name="[TENANT] Orders",
 *     description="API Endpoints for managing Orders"
 * )
 */
class OrderController extends ApiController
{
    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var OrderManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        parent::__construct();
        $this->transService = $service;
        $this->manager = new OrderManager($this->transService);
    }



    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="[TENANT] Get a list of orders",
     *     tags={"[TENANT] Orders"},
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
     *         description="Filter by Order ID(s). Can be array or single value.",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"))
     *     ),
     *     @OA\Parameter(
     *         name="dish",
     *         in="query",
     *         description="Filter by Dish ID(s). Can be array or single value.",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"))
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by Order Status",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="string"))
     *     ),
     *     @OA\Parameter(
     *         name="bill",
     *         in="query",
     *         description="Filter by Bill ID(s). Can be array or single value.",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"))
     *     ),
     *     @OA\Parameter(
     *         name="paid",
     *         in="query",
     *         description="Filter by Paid Status",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="order[id]",
     *         in="query",
     *         description="Order by ID",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Parameter(
     *         name="order[createdAt]",
     *         in="query",
     *         description="Order by creation date",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of orders",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/OrderResource")),
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
    public function index(OrderRequest $request): AnonymousResourceCollection
    {
        $limit = $request->query->get('itemsPerPage', OrderResource::LIMIT);
        $offset = ($request->query->get('page', 1) - 1) * $limit;
        $order = $request->input('order', ['id' => 'asc']);
        $criteria = [
            'id' => (array) $request->id,
            'dish' => (array) $request->dish,
            'status' => (array) $request->status,
            'bill' => (array) $request->bill,
        ];
        $user = Auth::user();
        $criteria['user'] = (array) ($user->isEndUserRole() ? [$user->id] : $request->user);
        $criteria['table'] = (array) ($user->hasRoles([User::ROLE_TABLE, User::ROLE_USER]) ? ['user' => $user->id] : $request->table);
        ! $request->has('paid') ?: $criteria['paid'] = $request->paid;

        return OrderResource::collection(Order::getRows($criteria, $order, $limit, $offset));
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="[TENANT] Get an order by ID",
     *     tags={"[TENANT] Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Order ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order resource",
     *         @OA\JsonContent(ref="#/components/schemas/OrderResource")
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
    public function show(Order $order): OrderResource
    {
        $this->authorize('view', $order);

        $user_logged = Auth::user();
        throw_if($user_logged->isEndUserRole() && $user_logged->id != $order->user_id, new AccessDeniedHttpException(__('admin.Action prohibited')));

        return new OrderResource($order);
    }


    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="[TENANT] Create a new order",
     *     tags={"[TENANT] Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="dish", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="bill", type="integer", example=100),
     *                 @OA\Property(property="paid", type="boolean", example=false),
     *                 @OA\Property(property="user", type="integer", example=1),
     *                 @OA\Property(property="table", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/OrderResource")
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
    public function store(OrderRequest $request): OrderResource
    {
        $this->authorize('create', Order::class);

        return (new OrderResource($this->manager->create($request)->fresh()))->withStatusCode(201);
    }


    /**
     * @OA\Put(
     *     path="/api/orders/{id}",
     *     summary="[TENANT] Update an existing order",
     *     tags={"[TENANT] Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Order ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="dish", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="completed"),
     *                 @OA\Property(property="bill", type="integer", example=120),
     *                 @OA\Property(property="paid", type="boolean", example=true),
     *                 @OA\Property(property="user", type="integer", example=1),
     *                 @OA\Property(property="table", type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/OrderResource")
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
    public function update(OrderRequest $request, Order $order): OrderResource
    {
        $this->authorize('update', $order);

        $user_logged = Auth::user();
        throw_if($user_logged->isEndUserRole() && $user_logged->id != $order->user_id, new AccessDeniedHttpException(__('admin.Action prohibited')));

        return new OrderResource($this->manager->update($request, $order)->fresh());
    }


    /**
     * @OA\Delete(
     *     path="/api/orders/{id}",
     *     summary="[TENANT] Delete an order by ID",
     *     tags={"[TENANT] Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Order ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order deleted successfully",
     *         @OA\JsonContent(ref="#/components/schemas/OrderResource")
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
    public function destroy(Order $order): OrderResource
    {
        $this->authorize('delete', $order);

        $user_logged = Auth::user();
        throw_if($user_logged->isEndUserRole() && $user_logged->id != $order->user_id, new AccessDeniedHttpException(__('admin.Action prohibited')));

        return new OrderResource($this->manager->delete($order));
    }
}
