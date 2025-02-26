<?php

namespace App\Managers;

use App\Enum\FriendInviteMethod;
use App\Enum\FriendStatus;
use App\Events\Friends\FriendEvent;
use App\Exceptions\ApiExceptions\Friend\FriendUserNotFound;
use App\Http\Controllers\ParametersTrait;
use App\Models\FireBaseNotificationV2;
use App\Models\FoodCategory;
use App\Models\Friend;
use App\Models\User;
use App\Models\UserSystem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FriendManager
{
    use ParametersTrait;

    /**
     * @param Request $request
     *
     * @return Friend $friend
     */
    public function create(Request $request): Friend
    {
        $receiver_data = $this->getParams($request, ['receiverId', 'phone', 'email']);
        $receiver = null;
        $friend_invite_method =null;
        if (isset($receiver_data['receiverId'])) {
            $receiver = UserSystem::find($receiver_data['receiverId']);
            $friend_invite_method = FriendInviteMethod::QR_CODE->value;
        } elseif (isset($receiver_data['phone'])) {
            $receiver = UserSystem::select()->where('phone', $receiver_data['phone'])->first();
            $friend_invite_method = FriendInviteMethod::PHONE->value;
        } elseif (isset($receiver_data['email'])) {
            $receiver = UserSystem::select()->where('email', $receiver_data['email'])->first();
            $friend_invite_method = FriendInviteMethod::EMAIL->value;
        }
        if (! isset($receiver) && ! $receiver instanceof UserSystem) {
            try {
                broadcast(new FriendEvent($receiver_data['receiverId'], auth()->user()->id, FriendStatus::NotFound->value));
            }catch
            (Exception $e){
                Log::error($e->getMessage());
            }
            throw new FriendUserNotFound($receiver_data);
        }
        $current_user_id = auth()->user()->id;

        $friend = Friend::where(function ($query) use ($current_user_id, $receiver) {
            $receiver_id = $receiver->id;
            $query->where('sender_id', $current_user_id)->where('receiver_id', $receiver_id);
        })->orWhere(function ($query) use ($current_user_id, $receiver) {
            $receiver_id = $receiver->id;
            $query->where('sender_id', $receiver_id)->where('receiver_id', $current_user_id);
        });


        $params['receiver_id'] = $receiver->id;
        $params['status'] = 0;
        $params['sender_id'] = auth()->user()->id;
        $params['friend_invite_method'] = $friend_invite_method;


        if (isset($params['receiver_id']) && isset($receiver)) {
            $friend = Friend::create($params)->fresh();

            if ($friend !== null) {
                broadcast(new FriendEvent($receiver->id, $current_user_id, FriendStatus::Pending->value));
//                NotificationService::sendPushToUser($friend->receiver_id, __('firebase.You have new friend invitation', ), 'friends', $friend->sender_id, NotificationTitle::ALERT);
                FireBaseNotificationV2::create([
                    'user_id' => $friend->receiver_id,
                    'title' => __('firebase.E-waiter'),
                    'body' => __('firebase.You have new friend invitation'),
                    'data' => json_encode([
                        'title' => __('firebase.E-waiter'),
                        'body' => __('firebase.You have new friend invitation'),
                        'url' => '/account/friends_screen',
                        'object_id' => $friend->sender_id,
                    ]),
                ]);
            }
        }



        return $friend;
    }

    /**
     * @param Request       $request
     * @param Friend        $friend
     *
     * @return Friend
     */
    public function update(Request $request, Friend $friend): Friend
    {
        $params = $this->getParams($request, ['status']);
        $current_user = auth()->user()->id;

        if ($friend->receiver_id == $current_user) {
            if (isset($params['status']) && $params['status'] == 1) {
                $success = $friend->update($params);

                if ($success) {

                    try {
                        broadcast(new FriendEvent($friend->sender_id, $friend->receiver_id, FriendStatus::Accepted->value));
                    }catch
                    (Exception $e){
                        Log::error($e->getMessage());
                    }
//                    broadcast(new FriendEvent($friend->sender_id, $friend->receiver_id, FriendStatus::Accepted->value));
//                    NotificationService::sendPushToUser($friend->sender_id, __('firebase.Your invitation has been accepted', ), 'friends', $friend->receiver_id, NotificationTitle::ALERT);
                    FireBaseNotificationV2::create([
                        'user_id' => $friend->sender_id,
                        'title' => __('firebase.E-waiter'),
                        'body' => __('firebase.Your invitation has been accepted'),
                        'data' => json_encode([
                            'title' => __('firebase.E-waiter'),
                            'body' => __('firebase.Your invitation has been accepted'),
                            'url' => '/account/friends_screen',
                            'object_id' => $friend->receiver_id,
                        ]),
                    ]);
                }
            }
        }

        $sender = User::where('id', $friend['sender_id'])->first();
        $receiver = User::where('id', $friend['receiver_id'])->first();
        $friend['sender_name'] = $sender ? $sender->first_name.' '.$sender->last_name : null;
        $friend['receiver_name'] = $receiver ? $receiver->first_name.' '.$receiver->last_name : null;
        if ($friend['sender_id'] == $current_user) {
            $friend['phone'] = $receiver ? Str::limit($receiver->phone, 3) : null;
            $friend['email'] = $receiver ? Str::limit($receiver->email, 3) : null;
        } elseif ($friend['receiver_id'] == $current_user) {
            $friend['phone'] = $sender ? $sender->phone : null;
            $friend['email'] = $sender ? $sender->email : null;
        }

        return $friend;

        // DB::connection('tenant')->transaction(function () use ($params, $references, $foodCategory) {
        //     if (!empty($references['removePhoto'])) {
        //         $foodCategory->photo()->where('id', $references['removePhoto']['id'])->delete();
        //     }

        //     if (isset($references['photo'])) {
        //         $foodCategory->photo()->updateOrCreate(['id' => $foodCategory->photo->id ?? null], ['filename' => $references['photo']]);
        //     }

        //     if (!empty($params)) {
        //         $foodCategory->update($params);
        //         $foodCategory->fresh();
        //     }

        //     if (isset($references['availability'])) {
        //         $references['availability']['food_category_id'] = $foodCategory->id;
        //         $foodCategory->availability()->updateOrCreate(['food_category_id'=>$foodCategory->id],$references['availability']);
        //     }
        // });
    }

    /**
     * @param FoodCategory $foodCategory
     *
     * @return FoodCategory
     */
    public function delete(Friend $friend): Friend
    {

        $status = DB::connection('tenant')->table('friends')->where('id', $friend->id)->value('status');

        // Logowanie statusu
        Log::info("Friend status: {$status}, Friend ID: {$friend->id}");

        // Sprawdź status i wyślij odpowiedni event
        if ($status == Friend::STATUS_ACCEPTED) {
            broadcast(new FriendEvent($friend->friend_id, auth()->user()->id, FriendStatus::Removed->value));
        } else {
            broadcast(new FriendEvent($friend->friend_id, auth()->user()->id, FriendStatus::Rejected->value));
        }

        DB::connection('tenant')->transaction(function () use ($friend) {
            $friend->delete();
        });

        $friend['friend_id'] = $friend->sender_id == auth()->user()->id ? $friend->receiver_id : $friend->sender_id;
        $friend['friend_data'] = User::find($friend->friend_id);

        return $friend;
    }

}
