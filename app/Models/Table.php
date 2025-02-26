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
use Illuminate\Notifications\Notifiable;

class Table extends Model
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
        'people_number',
        'description',
        'active',
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
     * @var array
     */
    protected $attributes = [
        'active' => 0,
        'people_number' => 0,
    ];

    protected static $activeName = [
        0 => 'no',
        1 => 'yes',
    ];

    public function getActiveName()
    {
        return self::$activeName[$this->active];
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function qr_code(): HasOne
    {
        return $this->hasOne(QRCode::class, 'object_id', 'id')->where('object_type', 'table');
    }

    /**
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function playerIds(): HasMany
    {
        return $this->hasMany(PlayerId::class, 'user_id', 'user_id');
    }

    /**
     * @return HasMany
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'table_id', 'id');
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
        if (! empty($criteria['withOrders'])) {
            $query->with(['orders' => function ($query) {
                $query->where('paid', '=', Order::PAID_NO)
                    ->where('status', '!=', Order::STATUS_CANCELED);
            }]);
        }
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
        $query = self::select('tables.*');

        if (! empty($filter)) {
            $query->where('name', 'LIKE', '%'.$filter.'%')
                ->orWhere('number', 'LIKE', '%'.$filter.'%');
        }
        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                $query->orderBy(self::decamelize($column), $direction);
            }
        }

        return $query->paginate($paginateSize, ['tables.id']);
    }

    public function scopeWithNumber(Builder $builder, int|string $number)
    {
        return $builder->where('number', $number);
    }

    public function scopeActive(Builder $builder)
    {
        return $builder->where('active', true);
    }

}
