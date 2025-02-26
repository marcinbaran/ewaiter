<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class Payment extends Model
{
    use ModelTrait;
    use UsesTenantConnection;

    /**
     * @var int
     */
    public const PAID_NO = 0;

    /**
     * @var int
     */
    public const PAID_YES = 1;

    /**
     * @var int
     */
    public const PAID_REFUNDED = 2;

    /**
     * @var string
     */
    public const TRANSFERRED_YES = 2;

    /**
     * @var string
     */
    public const TRANSFERRED_NO = 1;

    /**
     * @var string
     */
    public const TRANSFERRED_NA = 0;

    /**
     * @var array
     */
    protected $fillable = [
        'bill_id',
        'email',
        'hash',
        'p24_session_id',
        'p24_amount',
        'p24_currency',
        'p24_token',
        'transferred',
        'type',
        'url',
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
        'paid' => self::PAID_NO,
        'transferred' => self::TRANSFERRED_NA,
    ];

    /**
     * @var array
     */
    protected $casts = [
        'p24_last_response' => 'array',
    ];

    protected static $statusName = [
        self::PAID_NO => 'No',
        self::PAID_YES => 'Yes',
        self::PAID_REFUNDED => 'Refunded',
    ];

    /**
     * @return BelongsTo
     */
    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class, 'bill_id', 'id');
    }

    public function getStatusName()
    {
        return self::$statusName[$this->paid];
    }

    /**
     * @return BelongsTo
     */
    public function refund(): HasOne
    {
        return $this->hasOne(Refund::class, 'payment_id', 'id');
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
        $query = self::with('bill')->limit($limit)->offset($offset);

        if (! empty($criteria['id'])) {
            $query->whereIn('id', $criteria['id']);
        }
        if (! empty($criteria['bill'])) {
            $query->whereIn('bill_id', $criteria['bill']);
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
     * @param array       $filter_columns
     * @param bool       $ajax
     *
     * @return LengthAwarePaginator
     */
    public static function getPaginatedForPanel(string $filter = null, int $paginateSize, array $order = null, array $filter_columns = null, bool $ajax = false): LengthAwarePaginator
    {
        $query = self::distinct();
        if (! $ajax) {
            $query->with('bill');
        }

        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                $query->orderBy(self::decamelize($column), $direction);
            }
        }

        if (! empty($filter_columns)) {
            foreach ($filter_columns as $filter_column => $value) {
                if ($value !== null) {
                    $query->where($filter_column, $value);
                }
            }
        }

        if (! empty($filter)) {
            $query->where('id', 'LIKE', '%'.$filter.'%');
            $query->orWhere('bill_id', 'LIKE', '%'.$filter.'%');
            $query->orWhere('p24_amount', 'LIKE', '%'.$filter.'%');
            $query->orWhere('p24_currency', 'LIKE', '%'.$filter.'%');
            $query->orWhere('paid', 'LIKE', '%'.$filter.'%');
        }

        return $query->paginate($paginateSize, ['payments.*']);
    }
}
