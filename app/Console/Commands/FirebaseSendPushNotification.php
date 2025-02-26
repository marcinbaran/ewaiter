<?php

namespace App\Console\Commands;

use App\Models\FireBaseNotificationV2;
use App\Models\UserFCMToken;
use App\Services\FirebaseServiceV2;
use Illuminate\Console\Command;

class FirebaseSendPushNotification extends Command
{
    protected $signature = 'firebase:send-push-notification';
    protected $description = 'Firebase - set push notification';

    public function __construct(private readonly FirebaseServiceV2 $firebaseServiceV2)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $rows = FireBaseNotificationV2::where('status', 1)->get();

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
