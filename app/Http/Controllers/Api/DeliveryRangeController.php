<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\DeliveryRangeRequest;
use App\Http\Resources\Api\DeliveryRangeResource;
use App\Managers\DeliveryRangeManager;
use App\Models\DeliveryRange;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(name="[TENANT] DeliveryRange", description="[TENANT] API Endpoints for Delivery Ranges")
 */
class DeliveryRangeController extends ApiController
{
    /**
     * @var DeliveryRangeManager
     */
    private $manager;

    public function __construct()
    {
        parent::__construct();
        $this->middleware('can:view,delivery_range', ['only' => ['show']]);
        $this->middleware('can:update,delivery_range', ['only' => ['update']]);
        $this->middleware('can:delate,delivery_range', ['only' => ['destroy']]);
        $this->manager = new DeliveryRangeManager();
    }
    /**
     * @OA\Get(
     *     path="/delivery-ranges",
     *     summary="[TENANT] List all delivery ranges",
     *     tags={"[TENANT] DeliveryRange"},
     *     @OA\Parameter(
     *         name="itemsPerPage",
     *         in="query",
     *         required=false,
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", example=20)
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
     *         @OA\Schema(type="string", example="asc")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of delivery ranges",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/DeliveryRangeResource")
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
    public function index(DeliveryRangeRequest $request): AnonymousResourceCollection
    {
        $limit = $request->query->get('itemsPerPage', DeliveryRangeResource::LIMIT);
        $offset = ($request->query->get('page', 1) - 1) * $limit;
        $order = $request->input('order', ['id' => 'asc']);
        $user = Auth::user();

        $criteria = [
            'id' => (array) $request->id,
            'noLimit' => $request->query->get('noLimit', false),
        ];

        return DeliveryRangeResource::collection(DeliveryRange::getRows($criteria, $order, $limit, $offset));
    }


    /**
     * @OA\Get(
     *     path="/delivery-ranges/{id}",
     *     summary="[TENANT] Get a specific delivery range",
     *     tags={"[TENANT] DeliveryRange"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the delivery range",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Details of the delivery range",
     *         @OA\JsonContent(ref="#/components/schemas/DeliveryRangeResource")
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
     *         description="Delivery Range not found"
     *     )
     * )
     */
    public function show(DeliveryRange $delivery_range): DeliveryRangeResource
    {
        return new DeliveryRangeResource($delivery_range);
    }

    /**
     * @OA\Post(
     *     path="/delivery-ranges",
     *     summary="[TENANT] Create a new delivery range",
     *     tags={"[TENANT] DeliveryRange"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/DeliveryRange_POST")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Delivery range created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/DeliveryRangeResource")
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
    public function store(DeliveryRangeRequest $request): DeliveryRangeResource
    {
        return (new DeliveryRangeResource($this->manager->create($request)->fresh()))->withStatusCode(201);
    }

    /**
     * @OA\Put(
     *     path="/delivery-ranges/{id}",
     *     summary="[TENANT] Update an existing delivery range",
     *     tags={"[TENANT] DeliveryRange"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the delivery range",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(ref="#/components/schemas/DeliveryRange_PUT")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Delivery range updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/DeliveryRangeResource")
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
    public function update(DeliveryRangeRequest $request, DeliveryRange $delivery_range): DeliveryRangeResource
    {
        return new DeliveryRangeResource($this->manager->update($request, $delivery_range)->fresh());
    }


    /**
     * @OA\Delete(
     *     path="/delivery-ranges/{id}",
     *     summary="[TENANT] Delete a specific delivery range",
     *     tags={"[TENANT] DeliveryRange"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the delivery range",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Delivery range deleted successfully"
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
    public function destroy(DeliveryRange $delivery_range): DeliveryRangeResource
    {
        return new DeliveryRangeResource($this->manager->delete($delivery_range));
    }
}
