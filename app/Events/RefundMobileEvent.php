<?php

namespace App\Events;

use App\Managers\NotificationManager;
use App\Models\Refund;
use App\Services\TranslationService;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RefundMobileEvent
{
    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    /**
     * @var Refund
     */
    protected $refund;

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
    public function __construct(Refund $refund)
    {
        $this->refund = $refund;
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
     * @return RefundStatusMobileEvent
     */
    public function notificationMobileSent(): self
    {
        $request = request();
        $request->request->add(['type' => 'refund_mobile', 'refund' => ['id' => $this->refund->id], 'url' => Route('admin.refunds.show', ['refund' => $this->refund->id])]);
        $this->manager->create($request);

        return $this;
    }
}
