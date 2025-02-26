<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\NotificationRequest;
use App\Http\Resources\Api\NotificationResource;
use App\Managers\NotificationManager;
use App\Models\Notification;
use App\Models\Table;
use App\Notifications\Waiter;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
/**
 * @OA\Tag(
 *     name="[TENANT] Notifications",
 *     description="API for managing notifications"
 * )
 */
class NotificationController extends ApiController
{
    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var NotificationManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        parent::__construct();
        $this->transService = $service;
        $this->manager = new NotificationManager($this->transService);
    }
    /**
     * @OA\Get(
     *     path="/api/notifications",
     *     summary="[TENANT]Get a list of notifications",
     *     tags={"[TENANT] Notifications"},
     *     @OA\Parameter(
     *         name="itemsPerPage",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         description="Order by field and direction",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/NotificationResource"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index(NotificationRequest $request): AnonymousResourceCollection
    {
        $limit = $request->query->get('itemsPerPage', NotificationResource::LIMIT);
        $offset = ($request->query->get('page', 1) - 1) * $limit;
        $order = $request->input('order', ['id' => 'asc']);
        $criteria = [
            'id' => (array) $request->id,
            'type' => (array) $request->type,
            'table' => (array) $request->table,
            'fromDate' => $request->fromDate,
            'toDate' => $request->toDate,
        ];

        ! $request->has('isRead') ?: $criteria['isRead'] = $request->isRead;

        return NotificationResource::collection(Notification::getRows($criteria, $order, $limit, $offset));
    }
    /**
     * @OA\Get(
     *     path="/api/notifications/{id}",
     *     summary="[TENANT] Get a specific notification",
     *     tags={"[TENANT] Notifications"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Notification ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/NotificationResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Notification not found"
     *     )
     * )
     */
    public function show(Notification $notification): NotificationResource
    {
        $this->authorize('view', $notification);

        return new NotificationResource($notification);
    }
    /**
     * @OA\Post(
     *     path="/api/notifications",
     *     summary="[TENANT] Create a new notification",
     *     tags={"[TENANT] Notifications"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/NotificationPost")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Notification created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/NotificationResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Validation failed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function store(NotificationRequest $request)
    {
        $this->authorize('create', Notification::class);

        return (new NotificationResource($this->manager->create($request)))->withStatusCode(201);
    }

    /**
     * @OA\Put(
     *     path="/api/notifications/{id}",
     *     summary="[TENANT] Update an existing notification",
     *     tags={"[TENANT] Notifications"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Notification ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/NotificationPut")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/NotificationResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Notification not found"
     *     )
     * )
     */
    public function update(NotificationRequest $request, Notification $notification): NotificationResource
    {
        $this->authorize('update', $notification);

        return new NotificationResource($this->manager->update($request, $notification)->fresh());
    }

    /**
     * @OA\Delete(
     *     path="/api/notifications/{id}",
     *     summary="[TENANT] Delete a notification",
     *     tags={"[TENANT] Notifications"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Notification ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification deleted successfully",
     *         @OA\JsonContent(ref="#/components/schemas/NotificationResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Notification not found"
     *     )
     * )
     */
    public function destroy(Notification $notification): NotificationResource
    {
        $this->authorize('delete', $notification);

        return new NotificationResource($this->manager->delete($notification));
    }
}
