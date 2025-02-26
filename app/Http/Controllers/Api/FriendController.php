<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\FriendRequest;
use App\Http\Resources\Api\FriendResource;
use App\Managers\FriendManager;
use App\Models\Friend;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="[MOB] Friend",
 *     description="[MOB] API Endpoints for managing friends"
 * )
 */
class FriendController extends ApiController
{
    /**
     * var FriendsManager.
     */
    private $manager;

    public function __construct()
    {
        parent::__construct();
        $this->manager = new FriendManager();
    }

    /**
     * @OA\Get(
     *     path="/api/friends",
     *     operationId="getFriendsList",
     *     tags={"[MOB] Friend"},
     *     summary="[MOB] Get list of friends",
     *     description="Returns list of friends",
     *     @OA\Parameter(
     *         name="itemsPerPage",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=10
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         description="Order of items",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             example="id,asc"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="requestsOnly",
     *         in="query",
     *         description="Filter only friend requests",
     *         required=false,
     *         @OA\Schema(
     *             type="boolean"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/FriendResource")
     *     )
     * )
     */

    public function index(FriendRequest $request)
    {
        $limit = $request->query->get('itemsPerPage', FriendResource::LIMIT);
        $offset = ($request->query->get('page', 1) - 1) * $limit;
        $order = $request->input('order', ['id' => 'asc']);
        $criteria = [
            'status' => $request->query->get('status'),
            'requestsOnly' => $request->query->get('requestsOnly'),
        ];

        return FriendResource::collection(Friend::getRows($criteria, $order, $limit, $offset));
    }
    /**
     * @OA\Get(
     *     path="/api/friends/{id}",
     *     operationId="getFriendById",
     *     tags={"[MOB] Friend"},
     *     summary="[MOB] Get a friend by ID",
     *     description="Returns a friend by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of friend to return",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/FriendResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Friend not found"
     *     )
     * )
     */
    public function show(Friend $friend)
    {
        // $this->authorize('view', $friend);

        return new FriendResource($friend);
    }
    /**
     * @OA\Post(
     *     path="/api/friends",
     *     operationId="storeFriend",
     *     tags={"[MOB] Friend"},
     *     summary="[MOB] Create new friend request",
     *     description="Create a new friend request",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/FriendRequestPOST")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Friend request created",
     *         @OA\JsonContent(ref="#/components/schemas/FriendResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function store(FriendRequest $request)
    {
        // $this->authorize('create', Friend::class);

        $friend = $this->manager->create($request)->fresh();
        $friend['friend_data'] = User::find($friend->receiver_id);

        return (new FriendResource($friend))->withStatusCode(201);
    }
    /**
     * @OA\Put(
     *     path="/api/friends/{id}",
     *     operationId="updateFriend",
     *     tags={"[MOB] Friend"},
     *     summary="[MOB] Update a friend",
     *     description="Update friend information",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of friend to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/FriendRequestPUT")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Friend updated",
     *         @OA\JsonContent(ref="#/components/schemas/FriendResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function update(FriendRequest $request, Friend $friend)
    {
        // $this->authorize('update', $friend);
        $friend = $this->manager->update($request, $friend);

        return new FriendResource($friend);
    }
    /**
     * @OA\Delete(
     *     path="/api/friends/{id}",
     *     operationId="deleteFriend",
     *     tags={"[MOB] Friend"},
     *     summary="[MOB] Delete a friend",
     *     description="Delete a friend by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of friend to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Friend deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Friend not found"
     *     )
     * )
     */
    public function destroy(Friend $friend)
    {
        // $this->authorize('delete', $friend);
        $currentUser = auth()->user()->id;

        return new FriendResource($this->manager->delete($friend));
        // return new FriendResource($friend);
    }
}
