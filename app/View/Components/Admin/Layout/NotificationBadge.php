<?php

namespace App\View\Components\Admin\Layout;

use App\Models\FirebaseNotification;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NotificationBadge extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.layout.notification-badge', $this->getNotifiactionsCount());
    }

    private function getNotifiactionsCount()
    {
        $notifications = FirebaseNotification::query()
            ->where('user_id', auth()->user()->id)
            ->where('read_at', null)
            ->orderBy('created_at', 'DESC')
            ->get();

        return [
            'notificationsCount' => $notifications->count(),
        ];
    }
}
