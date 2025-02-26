<?php

namespace App\Http\Livewire\Dashboard;

use App\Models\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;

class Notifications extends Component
{
    public Collection $notifications;

    public array $notificationBefore;

    public array $notificationAfter;

    public int $notificationsCount = 0;

    public int $offset = 0;

    public int $perPage = 8;

    public string $class = '';

    protected $listeners = ['refreshDashboardNotifications' => 'refresh', 'scrollUpDashboardNotifications' => 'scrollUp', 'scrollDownDashboardNotifications' => 'scrollDown'];

    public function mount()
    {
        $this->reloadNotifications();
    }

    public function refresh()
    {
        $this->reloadNotifications();
    }

    public function render()
    {
        return view('livewire.dashboard.notifications');
    }

    public function markAsComplete($id)
    {
        $notification = Notification::find($id);
        $now = Carbon::now();
        if ($notification) {
            $notification->read_at = $now;
            $notification->updated_at = $now;
            $notification->update();
            $this->refresh();
        }
    }

    public function scrollUp()
    {
        if ($this->offset === 0) {
            return;
        }
        $this->offset--;
        $this->reloadNotifications();
    }

    public function scrollDown()
    {
        if ($this->offset === $this->notificationsCount - $this->perPage) {
            return;
        }
        $this->offset++;
        $this->reloadNotifications();
    }

    private function getNotificationBeforeOffset(): array
    {
        $notification = [];
        if ($this->offset > 0) {
            $notification = Notification::where('read_at', null)->orderBy('created_at', 'desc')->limit(1)->offset($this->offset - 1)->first();
        }

        return $this->transformNotification($notification ?? []);
    }

    private function getNotificationAfterOffset(): array
    {
        $notification = [];
        if ($this->offset < $this->notificationsCount - $this->perPage) {
            $notification = Notification::where('read_at', null)->orderBy('created_at', 'desc')->limit(1)->offset($this->offset + $this->perPage)->first();
        }

        return $this->transformNotification($notification ?? []);
    }

    private function getNotifications(): Collection
    {
        $notifications = Notification::where('read_at', null)->orderBy('created_at', 'desc')->limit($this->perPage)->offset($this->offset)->get();

        return $notifications;
    }

    private function getNotificationsCount(): int
    {
        return Notification::where('read_at', null)->count();
    }

    private function transformNotification($notification): array
    {
        return [
            'id' => $notification['id'] ?? null,
            'title' => $notification['title'] ?? '',
            'description' => $notification['description'] ?? '',
            'created_at' => Carbon::parse($notification['created_at'] ?? '')->diffForHumans(),
        ];
    }

    private function transformNotifications(Collection $notifications): Collection
    {
        $transformedNotifications = collect([]);
        foreach ($notifications as $notification) {
            $transformedNotifications->push($this->transformNotification($notification));
        }

        return $transformedNotifications;
    }

    private function reloadNotifications()
    {
        $this->notificationsCount = $this->getNotificationsCount();
        $this->notifications = $this->transformNotifications($this->getNotifications());
        $this->notificationBefore = $this->getNotificationBeforeOffset();
        $this->notificationAfter = $this->getNotificationAfterOffset();

        $this->dispatchBrowserEvent($this->notificationBefore['id'] ? 'hasDashboardNotificationBefore' : 'missingDashboardNotificationBefore');
        $this->dispatchBrowserEvent($this->notificationAfter['id'] ? 'hasDashboardNotificationAfter' : 'missingDashboardNotificationAfter');
        $this->dispatchBrowserEvent('refreshedDashboardNotifications');
        $this->dispatchBrowserEvent('rerenderScrollBar');
    }
}
