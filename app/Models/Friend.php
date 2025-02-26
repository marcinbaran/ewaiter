<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Friend extends Model
{
    use ModelTrait,
        Notifiable,
        UsesTenantConnection;

    /**
     * @var tinyint
     */
    public const STATUS_PENDING = 0;

    /**
     * @var tinyint
     */
    public const STATUS_ACCEPTED = 1;

    /**
     * @var array
     */
    public $statuses = [
        '0' => 'STATUS_PENDING',
        '1' => 'STATUS_ACCEPTED',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'status',
        'friend_invite_method'
    ];



    /**
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Get the user that owns the Friend.
     *
     * @return BelongsTo
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    /**
     * Get the user that owns the Friend.
     *
     * @return BelongsTo
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }

    /**
     * @param array $criteria
     * @param array $order
     * @param int   $limit
     * @param int   $offset
     *
     * @return Collection
     */
    public static function getRows($criteria, $order, $limit, $offset)
    {
        $current_user = auth()->user()->id;

        if (isset($criteria['requestsOnly'])) {
            $query = self::where('receiver_id', $current_user)->where('status', 0);
        } else {
            $query = self::where(function ($query) use ($current_user) {
                $query->where('sender_id', $current_user)->orWhere('receiver_id', $current_user);
            });

            if (isset($criteria['status'])) {
                $query->where('status', $criteria['status']);
            }
        }

        if (isset($limit)) {
            $query->limit($limit);
        }
        if (isset($offset)) {
            $query->offset($offset);
        }

        $friends = $query->get();
        foreach ($friends as $friend) {
            $sender = User::where('id', $friend['sender_id'])->first();
            $receiver = User::where('id', $friend['receiver_id'])->first();

            if ($sender && !empty($sender->first_name) && !empty($sender->last_name)) {
                $friend['sender_name'] = $sender->first_name . ' ' . $sender->last_name;
            } else {
                $friend['sender_name'] = $sender ? $sender->email : null;
            }

            if ($receiver && !empty($receiver->first_name) && !empty($receiver->last_name)) {
                $friend['receiver_name'] = $receiver->first_name . ' ' . $receiver->last_name;
            } else {
                $friend['receiver_name'] = $receiver ? $receiver->email : null;
            }
            if ($friend['sender_id'] == $current_user) {
                $friend['phone'] = $receiver ? $receiver->phone : null;
                $friend['email'] = $receiver ? $receiver->email : null;
            } elseif ($friend['receiver_id'] == $current_user) {
                $friend['phone'] = $sender ? $sender->phone : null;
                $friend['email'] = $sender ? $sender->email : null;
            }
        }

        return $friends;
    }
}
