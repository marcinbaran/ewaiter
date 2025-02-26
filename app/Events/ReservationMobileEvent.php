<?php

namespace App\Events;

use App\Managers\NotificationManager;
use App\Models\Reservation;
use App\Services\TranslationService;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReservationMobileEvent
{
    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    /**
     * @var Reservation
     */
    protected $reservation;

    /**
     * @var NotificationManager
     */
    protected $manager;

    /**
     * @var bool
     */
    private static $isMobileStatusChanged = false;

    /**
     * Create a new event instance.
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
        $translationService = new TranslationService(app('translation-manager'));
        $this->manager = new NotificationManager($translationService);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }

    /**
     * @return ReservationStatusMobileEvent
     */
    public function notificationMobileSent(): self
    {
        $request = request();
        $request->request->add(['type' => 'reservation_mobile', 'reservation' => ['id' => $this->reservation->id], 'url' => Route('admin.reservations.show', ['reservation' => $this->reservation->id])]);
        $this->manager->create($request);

        return $this;
    }
}
