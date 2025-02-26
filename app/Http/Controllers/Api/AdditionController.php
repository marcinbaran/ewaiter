<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AdditionRequest;
use App\Http\Resources\Api\AdditionResource;
use App\Managers\AdditionManager;
use App\Models\Addition;
use App\Models\User;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
/**
 * @OA\Tag(
 *     name="Additions",
 *     description="API Endpoints for managing additions"
 * )
 */
/**
 * Api for addition resource.
 */
class AdditionController extends ApiController
{
    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var AdditionManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        parent::__construct();
        //$this->middleware('can:view,App\\User,App\\FoodCategory', ['only' => ['index']]);
        $this->middleware('can:view,addition', ['only' => ['show']]);
        //$this->middleware('can:create,App\\FoodCategory', ['only' => ['store']]);
        $this->middleware('can:update,addition', ['only' => ['update']]);
        $this->middleware('can:delate,addition', ['only' => ['destroy']]);
        $this->transService = $service;
        $this->manager = new AdditionManager($this->transService);
    }
    /**
     * @OA\Get(
     *     path="/api/additions",
     *     operationId="getAllAdditions",
     *     tags={"[TENANT] Additions"},
     *     summary="[TENANT] Get collection of additions",
     *     description="Retrieve a list of additions based on the specified criteria.",
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
     *         description="Filter by specific IDs",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer")),
     *         example={1, 2, 3}
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         description="Order by field",
     *         required=false,
     *         @OA\Schema(
     *             type="object",
     *             @OA\Property(property="id", type="string", enum={"asc", "desc"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of additions",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Addition")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index(AdditionRequest $request): AnonymousResourceCollection
    {
        $limit = $request->query->get('itemsPerPage', AdditionResource::LIMIT);
        $offset = ($request->query->get('page', 1) - 1) * $limit;
        $order = $request->input('order', ['id' => 'asc']);
        $user = Auth::user();

        $criteria = [
            'id' => (array) $request->id,
            'dish' => (array) $request->dish,
            'noLimit' => $request->query->get('noLimit', false),
        ];
        $user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN]) ?: $criteria['visibility'] = true;

        return AdditionResource::collection(Addition::getRows($criteria, $order, $limit, $offset));
    }
    /**
     * @OA\Get(
     *     path="/api/additions/{id}",
     *     operationId="getAdditionById",
     *     tags={"[TENANT] Additions"},
     *     summary="[TENANT] Get a specific addition by ID",
     *     description="Retrieve a specific addition resource by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Addition ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Addition data",
     *         @OA\JsonContent(ref="#/components/schemas/Addition")
     *     ),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function show(Addition $addition): AdditionResource
    {
        return new AdditionResource($addition);
    }

    /**
     * @OA\Post(
     *     path="/api/additions",
     *     operationId="createAddition",
     *     tags={"[TENANT] Additions"},
     *     summary="[TENANT]Create a new addition",
     *     description="Create a new addition resource.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AdditionRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(ref="#/components/schemas/Addition")
     *     ),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function store(AdditionRequest $request): AdditionResource
    {
        return (new AdditionResource($this->manager->createFromRequest($request)->fresh()))->withStatusCode(201);
    }

    /**
     * @OA\Put(
     *     path="/api/additions/{id}",
     *     operationId="updateAddition",
     *     tags={"[TENANT] Additions"},
     *     summary="[TENANT] Update an existing addition",
     *     description="Update an existing addition resource.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Addition ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AdditionRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated",
     *         @OA\JsonContent(ref="#/components/schemas/Addition")
     *     ),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function update(AdditionRequest $request, Addition $addition): AdditionResource
    {
        return new AdditionResource($this->manager->updateFromRequest($request, $addition)->fresh());
    }


    /**
     * @OA\Delete(
     *     path="/api/additions/{id}",
     *     operationId="deleteAddition",
     *     tags={"[TENANT] Additions"},
     *     summary="[TENANT] Delete an addition",
     *     description="Delete an addition resource by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Addition ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deleted",
     *         @OA\JsonContent(ref="#/components/schemas/Addition")
     *     ),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function destroy(Addition $addition): AdditionResource
    {
        return new AdditionResource($this->manager->delete($addition));
    }
}
