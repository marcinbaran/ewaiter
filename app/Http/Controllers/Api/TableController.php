<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\TableRequest;
use App\Http\Resources\Api\TableResource;
use App\Managers\TableManager;
use App\Models\Order;
use App\Models\Table;
use App\Models\User;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Kalnoy\Nestedset\Collection;


/**
 * @OA\Tag(
 *     name="[TENANT] Tables",
 *     description="API Endpoints for managing tables."
 * )
 */
class TableController extends ApiController
{
    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var TableManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        parent::__construct();
        $this->middleware('can:view,App\\User,App\\Table', ['only' => ['index']]);
        $this->middleware('can:view,table', ['only' => ['show']]);
        $this->middleware('can:create,App\\Table', ['only' => ['store']]);
        $this->middleware('can:update,table', ['only' => ['update']]);
        $this->middleware('can:delate,table', ['only' => ['destroy']]);
        $this->transService = $service;
        $this->manager = new TableManager($this->transService);
    }


    /**
     * @OA\Get(
     *     path="/api/tables",
     *     operationId="getTables",
     *     tags={"[TENANT] Tables"},
     *     summary="[TENANT] Get a collection of tables",
     *     description="Retrieve a list of tables with optional filters.",
     *     @OA\Parameter(
     *         name="itemsPerPage",
     *         in="query",
     *         description="Number of items per page (max 50)",
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
     *         name="id",
     *         in="query",
     *         description="Table ID(s)",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"))
     *     ),
     *     @OA\Parameter(
     *         name="withOrders",
     *         in="query",
     *         description="Include tables with orders",
     *         required=false,
     *         @OA\Schema(type="boolean", default=false)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of tables",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/TableResource"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index(TableRequest $request): AnonymousResourceCollection
    {
        $limit = $request->query->get('itemsPerPage', TableResource::LIMIT);
        $offset = ($request->query->get('page', 1) - 1) * $limit;
        $order = $request->input('order', ['id' => 'asc']);
        $criteria = ['id' => (array) $request->id];
        $user = Auth::user();
        $criteria['withOrders'] = (int) ($user->hasRoles([User::ROLE_TABLE, User::ROLE_USER]) && ! $request->query->has('withOrders') ? true : $request->withOrders);

        return TableResource::collection(Table::getRows($criteria, $order, $limit, $offset));
    }
    /**
     * @OA\Get(
     *     path="/api/tables/{id}",
     *     operationId="getTableById",
     *     tags={"[TENANT] Tables"},
     *     summary="[TENANT] Get a table by ID",
     *     description="Retrieve details of a specific table by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the table",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Details of a table",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/TableResource")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Table not found"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     security={{"bearerAuth":{}}}
     * )
     */

    public function show(Table $table): TableResource
    {
        return new TableResource($table);
    }

    /**
     * @OA\Post(
     *     path="/api/tables",
     *     operationId="createTable",
     *     tags={"[TENANT] Tables"},
     *     summary="[TENANT] Create a new table",
     *     description="Create a new table with the provided details.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             ref="#/components/schemas/TableRequestPOST"
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Table created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/TableResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function store(TableRequest $request): TableResource
    {
        return (new TableResource($this->manager->create($request)->fresh()))->withStatusCode(201);
    }

    /**
     * @OA\Put(
     *     path="/api/tables/{id}",
     *     operationId="updateTable",
     *     tags={"[TENANT] Tables"},
     *     summary="[TENANT] Update a table",
     *     description="Update the details of an existing table.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the table",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             ref="#/components/schemas/TableRequestPUT"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Table updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/TableResource")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Table not found"),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function update(TableRequest $request, Table $table): TableResource
    {
        return new TableResource($this->manager->update($request, $table)->fresh());
    }
    /**
     * @OA\Delete(
     *     path="/api/tables/{id}",
     *     operationId="deleteTable",
     *     tags={"[TENANT] Tables"},
     *     summary="[TENANT] Delete a table",
     *     description="Delete a specific table by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the table",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Table deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/TableResource")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Table not found"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function destroy(Table $table): TableResource
    {
        return new TableResource($this->manager->delete($table));
    }
}
