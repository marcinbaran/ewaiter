<?php

namespace App\Jobs;

use App\Models\FireBaseNotificationV2;
use App\Models\UserFCMToken;
use App\Repositories\MultiTentantRepositoryTrait;
use App\Services\FirebaseServiceV2;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FirebaseSendPushNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, MultiTentantRepositoryTrait;

    public function __construct(private readonly FirebaseServiceV2 $firebaseServiceV2)
    {
    }

    public function handle(): void
    {
        $rows = FireBaseNotificationV2::where('status', 1)
            ->where('created_at', '>=', Carbon::now()->subMinutes(30))
            ->get();

        foreach ($rows as $row) {
            $tokens = UserFCMToken::where('user_id', $row->user_id)->get();
            foreach ($tokens as $token) {
                $response = $this->firebaseServiceV2->sendNotification($token->token, $row);

                if (isset($response['error'])) {
                    $row->status=0;
                } else {
                    $row->status=2;
                }

                $row->save();
            }
        }
    }
}
