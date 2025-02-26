<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\FirebaseNotificationResource;
use App\Models\FirebaseNotification;
use App\View\Components\Admin\Layout\Notification as NotificationComponent;
use App\View\Components\Admin\Layout\NotificationBadge;

class FirebaseNotificationController extends Controller
{
    public function __construct()
    {
        FirebaseNotificationResource::wrap('results');
    }

    public function show($id)
    {
        $user_id = auth()->user()->id;
        $notification = FirebaseNotification::query()->where('id', $id)->where('user_id', $user_id)->where('read_at', null)->first();
        if (! $notification) {
            session()->flash('alert-warning', __('admin.Wrong notification'));

            return redirect()->back();
        }

        $notification->read_at = date('Y-m-d H:i:s');
        $notification->save();

        session()->flash('alert-success', __('admin.Notification has been read'));

        return redirect()->back();
    }

    public function read()
    {
        $user_id = auth()->user()->id;
        $notifications = FirebaseNotification::query()->where('user_id', $user_id)->where('read_at', null)->get();

        if (! $notifications) {
            session()->flash('alert-warning', __('admin.No notifications to read'));

            return redirect()->back();
        }
        FirebaseNotification::query()->where('user_id', $user_id)->where('read_at', null)
                ->update(['read_at'=>date('Y-m-d H:i:s')]);
        session()->flash('alert-success', __('admin.Notifications has been read'));

        return redirect()->back();
    }

    public function reload()
    {
        $component = app(NotificationBadge::class);
        $html = $component->render();

        return [
            'status' => 'success',
            'data' => [
                'notification_html' => $html->__toString(),
            ],
        ];
    }

    public function refresh()
    {
        $component = app(NotificationComponent::class);
        $html = $component->render();

        return [
            'status' => 'success',
            'data' => [
                'notification_html' => $html->__toString(),
            ],
        ];
    }
}
