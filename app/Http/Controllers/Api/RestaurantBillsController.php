<?php

namespace App\Http\Controllers\Api;

use App\Enum\NotificationTitle;
use App\Http\Resources\Api\BillResource;
use App\Models\Bill;
use App\Models\FireBaseNotificationV2;
use App\Models\Notification;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Repositories\Eloquent\BillRepository;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
/**
 * @OA\Tag(
 *     name="[TENANT] Restaurant Bills",
 *     description="[TENANT] API Endpoints for managing restaurant bills and notifications."
 * )
 */
class RestaurantBillsController extends ApiController
{
    public function __construct(
        private BillRepository $billRepository
    ) {
        parent::__construct();
    }
    /**
     * @OA\Get(
     *     path="/bill/current",
     *     tags={"[TENANT] Restaurant Bills"},
     *     summary="[TENANT] Get current orders",
     *     description="Retrieve all bills with status 'accepted' or 'ready'.",
     *     @OA\Response(
     *         response=200,
     *         description="List of current orders",
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
    public function getCurrentOrders()
    {
        $actualOrders = $this->billRepository->getBillsWithStatus([Bill::STATUS_ACCEPTED, Bill::STATUS_READY]);

        return BillResource::collection($actualOrders);
    }
    /**
     * @OA\Get(
     *     path="/bill/new",
     *     tags={"[TENANT] Restaurant Bills"},
     *     summary="[TENANT] Get new orders",
     *     description="Retrieve all bills with status 'new'.",
     *     @OA\Response(
     *         response=200,
     *         description="List of new orders",
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
    public function getNewOrders()
    {
        $newOrders = $this->billRepository->getBillsWithStatus([Bill::STATUS_NEW]);

        return BillResource::collection($newOrders);
    }
    /**
     * @OA\Get(
     *     path="/restaurant/notifications",
     *     tags={"[TENANT] Restaurant Bills"},
     *     summary="Get notifications",
     *     description="Retrieve all unread notifications with types like 'Waiter', 'Alert', or 'Reservation'.",
     *     @OA\Response(
     *         response=200,
     *         description="List of notifications",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="New Reservation"),
     *                 @OA\Property(property="link", type="string", example="/admin/reservations/edit/1"),
     *                 @OA\Property(property="description", type="string", example="Table 5"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-31T12:34:56Z")
     *             )
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
    public function getNotifications()
    {
        $notifications = Notification::where('read_at', null)
            ->where(function ($query) {
                $query->where('type', 'Like', '%Waiter')
                    ->orWhere('type', 'Like', '%Alert')
                    ->orWhere('type', 'Like', '%Reservation');
            })
            ->orderBy('created_at', 'desc')->get();
        $result = $notifications->map(function (Notification $notification) {
            $tableId = ($notification->notifiable instanceof Reservation && $notification->notifiable->table) ? $notification->notifiable->table->name : false;
            $description = $notification->description ?? '';
            if ($tableId) {
                $description .= ', '.__('admin.notifications.table', ['table' => $tableId]);
            }

            $link = ($notification->notifiable instanceof Reservation) ? route('admin.reservations.edit', $notification->notifiable->id) : false;

            return [
                'id' => $notification['id'] ?? null,
                'title' => $notification['title'] ?? '',
                'link' => $link,
                'description' => $description,
                'created_at' => $notification['created_at'] ?? '',
            ];
        });

        return json_encode($result);
    }
    /**
     * @OA\Post(
     *     path="/restaurant/notification/mark-as-read",
     *     tags={"[TENANT] Restaurant Bills"},
     *     summary="[TENANT] Mark notification as read",
     *     description="Mark a specific notification as read.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification marked as read",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="read_at", type="string", format="date-time", example="2024-07-31T12:34:56Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Notification not found"
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
    public function markNotificationAsRead(Request $request)
    {
        $notification = Notification::find($request->id);
        if ($notification->read_at !== null) {
            return response(null, 404);
        } else {
            $notification->read_at = Carbon::now();
            $notification->save();

            return response($notification, 200);
        }
    }
    /**
     * @OA\Post(
     *     path="/bill/accept-new",
     *     tags={"[TENANT] Restaurant Bills"},
     *     summary="[TENANT] Accept bill",
     *     description="Change the status of a bill to 'accepted' and set the wait time.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="timeWait", type="integer", example=15)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bill accepted",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/BillResource"
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bill not found"
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
    public function acceptBill(Request $request)
    {
        $bill = Bill::find($request->id);
        $bill->status = Bill::STATUS_ACCEPTED;
        $bill->time_wait = Carbon::now()->addMinutes($request->timeWait)->format('Y-m-d H:i:s');
        $bill->save();

        return response($bill, 200);
    }
    /**
     * @OA\Post(
     *     path="/bill/cancel",
     *     tags={"[TENANT] Restaurant Bills"},
     *     summary="Cancel bill",
     *     description="[TENANT] Change the status of a bill to 'canceled'.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bill canceled",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/BillResource"
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bill not found"
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
    public function cancelBill(Request $request)
    {
        $bill = Bill::find($request->id);
        $bill->status = Bill::STATUS_CANCELED;
        $bill->save();

        return response($bill, 200);
    }
    /**
     * @OA\Put(
     *     path="/bill/update-status",
     *     tags={"[TENANT] Restaurant Bills"},
     *     summary="[TENANT] Update bill status",
     *     description="Update the status of a specific bill.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="status", type="string", example="completed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bill status updated",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/BillResource"
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bill not found"
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
    public function updateStatus(Request $request)
    {
        $bill = Bill::find($request->id);
        $bill->status = $request->status;
        $bill->save();

        return response($bill, 200);
    }
    /**
     * @OA\Put(
     *     path="/bill/update-time-wait",
     *     tags={"[TENANT] Restaurant Bills"},
     *     summary="[TENANT] Update wait time",
     *     description="Update the wait time for a specific bill and send a push notification to the user.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="timeWait", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Wait time updated and notification sent",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/BillResource"
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bill not found"
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
    public function updateWaitTime(Request $request)
    {
        $bill = Bill::find($request->id);
        $bill->time_wait = Carbon::parse($bill->time_wait)->addMinutes($request->timeWait)->format('Y-m-d H:i:s');
        $bill->save();

        $pushNotificationDescription = __(
            'firebase.The approximate delivery time of your order has been postponed',
            [
                'restaurant' => Restaurant::getCurrentRestaurant()->name,
                'time' => Carbon::parse($bill->time_wait)->format('H:i')]
        );

//        NotificationService::sendPushToUser($bill->user_id, $pushNotificationDescription, 'orders/'.$bill->id, $bill->id, NotificationTitle::STATUS_BILL);
        FireBaseNotificationV2::create([
            'user_id' => $bill->user_id,
            'title' => __('firebase.E-waiter'),
            'body' => $pushNotificationDescription,
            'data' => json_encode([
                'title' => __('firebase.E-waiter'),
                'body' => $pushNotificationDescription,
                'url' => '/account/orders_history/' . $bill->id,
                'object_id' => $bill->id,
            ]),
        ]);

        return response($bill, 200);
    }
}
