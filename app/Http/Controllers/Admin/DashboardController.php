<?php

namespace App\Http\Controllers\Admin;

use App\Enum\DeliveryMethod;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\StatisticResource;
use App\Http\Resources\Api\BillResource;
use App\Models\Bill;
use App\Models\Notification;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Repositories\Eloquent\BillRepository;
use App\Services\FirebaseServiceV2;
use Carbon\Carbon;
use Hyn\Tenancy\Facades\TenancyFacade;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private BillRepository $billRepository
    ) {
        StatisticResource::wrap('results');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function index(Request $request)
    {
        $viewFile = TenancyFacade::website() ? 'admin.dashboard.index' : 'admin.dashboard_system.index';

        return view($viewFile)->with([
            'controller' => 'dashboard',
            'action' => 'index',
        ]);
    }

    public function ordersFullscreen()
    {
        return view('admin.dashboard.orders_fullscreen');
    }

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
                'created_at' => Carbon::parse($notification['created_at'] ?? '')->diffForHumans(),
            ];
        });

        return json_encode($result);
    }

    public function markNotificationAsRead(string $id)
    {
        $notification = Notification::find($id);
        if ($notification->read_at !== null) {
            return response(null, 404);
        } else {
            $notification->read_at = Carbon::now();
            $notification->save();

            return response(null, 204);
        }
    }

    public function markAllNotificationAsRead()
    {
        Notification::where('read_at', null)
            ->where(function ($query) {
                $query->where('type', 'Like', '%Waiter')
                    ->orWhere('type', 'Like', '%Alert')
                    ->orWhere('type', 'Like', '%Reservation');
            })
            ->update(['read_at' => Carbon::now()]);

        return response(null, 204);
    }

    public function getNewOrders()
    {
        $newOrders = $this->billRepository->getBillsWithStatus([Bill::STATUS_NEW]);

        $restaurant = Restaurant::getCurrentRestaurant();
        $restaurant->last_activity_request_date = date('Y-m-d H:i:s');
        $restaurant->save();

        return BillResource::collection($newOrders);
    }

    public function getActualOrders()
    {
        $actualOrders = $this->billRepository->getBillsWithStatus([Bill::STATUS_ACCEPTED, Bill::STATUS_READY]);

        return BillResource::collection($actualOrders);
    }

    public function acceptBill(Request $request)
    {
        $bill = Bill::find($request->id);
        $bill->status = Bill::STATUS_ACCEPTED;
        $bill->time_wait = Carbon::now()->addMinutes($request->timeWait)->format('Y-m-d H:i:s');
        $bill->save();
        $deliveryTime = Carbon::now()->addMinutes($request->timeWait)->format('Y-m-d H:i');

        if ($bill->delivery_type == 'delivery_personal_pickup') {
            FirebaseServiceV2::saveNotification($bill->user_id, __('firebase.Your order has been accepted [with time, personal pickup]', ['delivery_time' => $deliveryTime]), '/account/orders_history/' . $bill->id, $bill->id);
        } elseif ($bill->delivery_type == 'delivery_table') {
            FirebaseServiceV2::saveNotification($bill->user_id, __('firebase.Your order has been accepted [with time, table delivery]', ['delivery_time' => $deliveryTime]), '/account/orders_history/' . $bill->id, $bill->id);
        } else {
            FirebaseServiceV2::saveNotification($bill->user_id, __('firebase.Your order has been accepted [with time]', ['delivery_time' => $deliveryTime]), '/account/orders_history/' . $bill->id, $bill->id);
        }


        return response(null, 204);
    }

    public function cancelBill(Request $request, int $id)
    {
        $bill = Bill::find($id);
        $bill->status = Bill::STATUS_CANCELED;
        $bill->save();

        return response(null, 204);
    }

    public function updateStatus(Request $request)
    {
        $bill = Bill::find($request->billId);
        $bill->status = $request->status;

        if ($bill->status === 3) {
            $bill->released_at = Carbon::now();
        }

        $bill->save();

        return response(null, 204);
    }

    public function updateWaitTime(Request $request, int $id)
    {
        $bill = Bill::find($id);
        $bill->time_wait = Carbon::parse($bill->time_wait)->addMinutes($request->timeWait)->format('Y-m-d H:i:s');
        $bill->save();

        if ($bill->delivery_type == 'delivery_personal_pickup') {
            $pushNotificationDescription = __(
                'firebase.The approximate pickup time of your order has been postponed',
                [
                    'restaurant' => Restaurant::getCurrentRestaurant()->name,
                    'time' => Carbon::parse($bill->time_wait)->format('H:i')]
            );
        } elseif ($bill->delivery_type == 'delivery_table') {
            $pushNotificationDescription = __(
                'firebase.The approximate table time of your order has been postponed',
                [
                    'restaurant' => Restaurant::getCurrentRestaurant()->name,
                    'time' => Carbon::parse($bill->time_wait)->format('H:i')]
            );
        } else {
            $pushNotificationDescription = __(
                'firebase.The approximate delivery time of your order has been postponed',
                [
                    'restaurant' => Restaurant::getCurrentRestaurant()->name,
                    'time' => Carbon::parse($bill->time_wait)->format('H:i')]
            );
        }

        FirebaseServiceV2::saveNotification($bill->user_id, $pushNotificationDescription, '/account/orders_history/' . $bill->id, $bill->id);

        return response(null, 204);
    }

    public function getServerTime()
    {
        $time = [
            'current_time' => Carbon::now()->toTimeString(),
            'current_date' => Carbon::now()->toDateString(),
        ];

        return response($time, 200);
    }
}
