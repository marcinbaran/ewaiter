<?php

namespace App\Services;

use App\Enum\NotificationObject;
use App\Enum\NotificationTitle;
use App\Models\FirebaseNotification;

class NotificationService
{
    // private function __construct() {}

    public static function createNotificationDatabaseEntry(NotificationTitle $notificationType, string $content, string $url, string $user_id, string $object_id, NotificationObject $object)
    {
//        if (config('app.env') != 'production') {
//            return;
//        }

        $firebaseNotification = new FirebaseNotification();
        $firebaseNotification->title = $notificationType->value;
        $firebaseNotification->content = __($content);
        $firebaseNotification->url = $url;
        $firebaseNotification->user_id = $user_id;
        $firebaseNotification->object_id = $object_id;
        $firebaseNotification->object = $object->value;
        $firebaseNotification->sent = 1;
        $firebaseNotification->save();
    }

    public static function sendPushToUser(
        string $user_id,
        string $content,
        string $url,
        string $object_id,
        NotificationTitle $type = NotificationTitle::STATUS_BILL
    ) {
        self::createNotificationDatabaseEntry($type, $content, $url, $user_id, $object_id, NotificationObject::MOBILE_TOPICS);
    }
}
