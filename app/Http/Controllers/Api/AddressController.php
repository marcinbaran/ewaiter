<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AddressRequest;
use App\Http\Resources\Api\AddressResource;
use App\Managers\AddressManager;
use App\Models\AddressSystem;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
/**
 * @OA\Tag(
 *     name="Address",
 *     description="API Endpoints for managing Addresses"
 * )
 */
/**
 * Api for address resource.
 */
class AddressController extends ApiController
{
    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var AddressManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        parent::__construct();
        $this->transService = $service;
        $this->manager = new AddressManager($this->transService);
    }

    /**
     * @OA\Get(
     *     path="/api/addresses",
     *     operationId="getAddresses",
     *     tags={"Addresses"},
     *     summary="Get collection of addresses",
     *     description="Retrieve a list of address resources.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="itemsPerPage",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", default=20, maximum=50),
     *         description="The number of items per page"
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", default=1),
     *         description="The collection page number"
     *     ),
     *     @OA\Parameter(
     *         name="noLimit",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean", default=false),
     *         description="Get the collection without limits"
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer")),
     *         description="Address ID(s)"
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="object",
     *             @OA\Property(property="id", type="string", enum={"asc", "desc"}),
     *             @OA\Property(property="created_at", type="string", enum={"asc", "desc"})
     *         ),
     *         description="Order by field"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Address")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function index(AddressRequest $request): AnonymousResourceCollection
    {
        $limit = $request->query->get('itemsPerPage', AddressResource::LIMIT);
        $offset = ($request->query->get('page', 1) - 1) * $limit;
        $order = $request->input('order', ['id' => 'asc']);

        $criteria = [
            'id' => (array) $request->id,
            'noLimit' => $request->query->get('noLimit', false),
        ];

        $user = Auth::user();
        $criteria['user'] = (array) ($user->isEndUserRole() ? [$user->id] : $request->user);

        return AddressResource::collection(AddressSystem::getRows($criteria, $order, $limit, $offset));
    }

    /**
     * @OA\Get(
     *     path="/api/addresses/{id}",
     *     operationId="getAddress",
     *     tags={"Addresses"},
     *     summary="Get a specific address resource by ID",
     *     description="Retrieve a specific address resource.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Address ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/Address")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Forbidden.")
     *         )
     *     )
     * )
     */
    public function show(AddressSystem $address): AddressResource
    {
        $user = Auth::user();
        throw_if($user->isEndUserRole() && ! $address->isUserAddress(), new AccessDeniedHttpException(__('admin.Action prohibited')));

        return new AddressResource($address);
    }

    /**
     * @OA\Post(
     *     path="/api/addresses",
     *     operationId="createAddress",
     *     tags={"Addresses"},
     *     summary="Create a new address resource",
     *     description="Create a new address resource.",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AddressRequest_POST")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(ref="#/components/schemas/Address")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function store(AddressRequest $request): JsonResource
    {
        return (new AddressResource($this->manager->create($request)->fresh()))->withStatusCode(Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/addresses/{id}",
     *     operationId="updateAddress",
     *     tags={"Addresses"},
     *     summary="Update an existing address resource",
     *     description="Update an existing address resource.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Address ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AddressRequest_PUT")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated",
     *         @OA\JsonContent(ref="#/components/schemas/Address")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Forbidden.")
     *         )
     *     )
     * )
     */
    public function update(AddressRequest $request, AddressSystem $address): AddressResource
    {
        $user = Auth::user();
        throw_if($user->isEndUserRole() && ! $address->isUserAddress(), new AccessDeniedHttpException(__('admin.Action prohibited')));

        return new AddressResource($this->manager->update($request, $address)->fresh());
    }

    /**
     * @OA\Delete(
     *     path="/api/addresses/{id}",
     *     operationId="deleteAddress",
     *     tags={"Addresses"},
     *     summary="Delete an address resource",
     *     description="Delete an address resource by ID.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Address ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deleted",
     *         @OA\JsonContent(ref="#/components/schemas/Address")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Forbidden.")
     *         )
     *     )
     * )
     */
    public function destroy(AddressSystem $address): AddressResource
    {
        $user = Auth::user();
        throw_if($user->isEndUserRole() && ! $address->isUserAddress(), new AccessDeniedHttpException(__('admin.Action prohibited')));

        return new AddressResource($this->manager->delete($address));
    }
}
