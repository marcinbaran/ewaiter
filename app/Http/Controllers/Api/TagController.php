<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\TagRequest;
use App\Http\Resources\Api\TagResource;
use App\Managers\TagManager;
use App\Models\Tag;
use App\Models\User;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="[TENANT] Tags",
 *     description="[TENANT] API Endpoints for managing tags."
 * )
 */
class TagController extends ApiController
{
    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var TagManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        parent::__construct();
        $this->middleware('can:view,tag', ['only' => ['show']]);
        $this->middleware('can:update,tag', ['only' => ['update']]);
        $this->middleware('can:delate,tag', ['only' => ['destroy']]);
        $this->transService = $service;
        $this->manager = new TagManager($this->transService);
    }
    /**
     * @OA\Get(
     *     path="/api/tags",
     *     operationId="getTags",
     *     tags={"[TENANT] Tags"},
     *     summary="[TENANT] Get a collection of tags",
     *     description="Retrieve a list of tags with optional filters and pagination.",
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
     *         description="Tag ID(s)",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"))
     *     ),
     *     @OA\Parameter(
     *         name="noLimit",
     *         in="query",
     *         description="Retrieve all tags without pagination",
     *         required=false,
     *         @OA\Schema(type="boolean", default=false)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of tags",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/TagResource"))
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     security={{"bearerAuth":{}}}
     * )
     */

    public function index(TagRequest $request): AnonymousResourceCollection
    {
        $limit = $request->query->get('itemsPerPage', TagResource::LIMIT);
        $offset = ($request->query->get('page', 1) - 1) * $limit;
        $order = $request->input('order', ['id' => 'asc']);
        $user = Auth::user();

        $criteria = [
            'id' => (array) $request->id,
            'noLimit' => $request->query->get('noLimit', false),
        ];

        return TagResource::collection(Tag::getRows($criteria, $order, $limit, $offset));
    }

    /**
     * @OA\Get(
     *     path="/api/tags/{id}",
     *     operationId="getTagById",
     *     tags={"[TENANT] Tags"},
     *     summary="[TENANT] Get a tag by ID",
     *     description="Retrieve a specific tag by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the tag",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Details of a tag",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/TagResource")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Tag not found"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function show(Tag $tag): TagResource
    {
        return new TagResource($tag);
    }


    /**
     * @OA\Post(
     *     path="/api/tags",
     *     operationId="createTag",
     *     tags={"[TENANT] Tags"},
     *     summary="[TENANT] Create a new tag",
     *     description="Create a new tag with the provided details.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             ref="#/components/schemas/TagRequestPOST"
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tag created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/TagResource")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function store(TagRequest $request): TagResource
    {
        return (new TagResource($this->manager->create($request)->fresh()))->withStatusCode(201);
    }
    /**
     * @OA\Put(
     *     path="/api/tags/{id}",
     *     operationId="updateTag",
     *     tags={"[TENANT] Tags"},
     *     summary="[TENANT] Update a tag",
     *     description="Update the details of an existing tag.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the tag",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             ref="#/components/schemas/TagRequestPUT"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/TagResource")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Tag not found"),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     security={{"bearerAuth":{}}}
     * )
     */

    public function update(TagRequest $request, Tag $tag): TagResource
    {
        return new TagResource($this->manager->update($request, $tag)->fresh());
    }
    /**
     * @OA\Delete(
     *     path="/api/tags/{id}",
     *     operationId="deleteTag",
     *     tags={"[TENANT] Tags"},
     *     summary="[TENANT] Delete a tag",
     *     description="Delete a specific tag by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the tag",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/TagResource")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Tag not found"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     security={{"bearerAuth":{}}}
     * )
     */

    public function destroy(Tag $tag): TagResource
    {
        return new TagResource($this->manager->delete($tag));
    }
}
