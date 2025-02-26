<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Expression;
use Illuminate\Notifications\Notifiable;

class Room extends Model
{
    use ModelTrait;
    use Notifiable;
    use UsesTenantConnection;
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'number',
        'floor',
        'redirect',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return HasMany
     */
//    public function orders(): HasMany
//    {
//        return $this->hasMany(Order::class, 'room_id', 'id');
//    }

    /**
     * @return HasMany
     */
    public function playerIds(): HasMany
    {
        return $this->hasMany(PlayerId::class, 'user_id', 'user_id');
    }

    /**
     * @return HasOne
     */
    public function qr_code(): HasOne
    {
        return $this->hasOne(QRCode::class, 'object_id', 'id')->where('object_type', 'room');
    }

    /**
     * @param array $criteria
     * @param array $order
     * @param int   $limit
     * @param int   $offset
     *
     * @return Collection
     */
    public static function getRows(array $criteria, array $order, int $limit, int $offset): Collection
    {
        $query = self::with('orders')->limit($limit)->offset($offset);

        if (! empty($criteria['id'])) {
            $query->whereIn('id', $criteria['id']);
        }
        if (! empty($criteria['user'])) {
            $query->whereIn('user_id', $criteria['user']);
        }
//        if (!empty($criteria['withOrders'])) {
//            $query->with(['orders' => function ($query) {
//                $query->where('paid', '=', Order::PAID_NO)
//                    ->where('status', '!=', Order::STATUS_CANCELED);
//            }]);
//        }
        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                $query->orderBy(self::decamelize($column), $direction);
            }
        }

        return $query->get();
    }

    /**
     * @param string|null $filter
     * @param int         $paginateSize
     * @param array       $order
     *
     * @return LengthAwarePaginator
     */
    public static function getPaginatedForPanel(string $filter = null, int $paginateSize, array $order = null): LengthAwarePaginator
    {
        $query = self::select('rooms.*')
            ->leftJoin('users', 'users.id', '=', 'rooms.user_id')
            ->with('user:id,first_name,last_name');

        if (! empty($filter)) {
            $query->where('rooms.name', 'LIKE', '%'.$filter.'%')
                ->orWhere('rooms.floor', 'LIKE', '%'.$filter.'%')
                ->orWhere('rooms.number', 'LIKE', '%'.$filter.'%')
                ->orWhere('users.first_name', 'LIKE', '%'.$filter.'%')
                ->orWhere('users.last_name', 'LIKE', '%'.$filter.'%');
        }
        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                $query->orderBy('user' == $column ? new Expression('CONCAT(`users`.`first_name`, `users`.`last_name`)') : self::decamelize($column), $direction);
            }
        }

        return $query->paginate($paginateSize, ['rooms.id']);
    }

    public function scopeWithNumber(Builder $builder, int|string $number)
    {
        return $builder->where('number', $number);
    }

}
