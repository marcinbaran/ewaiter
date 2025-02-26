<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class Refund extends Model
{
    use UsesTenantConnection;
    use ModelTrait,Notifiable;

    /**
     * @var string
     */
    public const STATUS_ERROR = 4;

    /**
     * @var string
     */
    public const STATUS_REFUNDED = 3;

    /**
     * @var string
     */
    public const STATUS_TO_REFUNDED = 2;

    /**
     * @var string
     */
    public const STATUS_REJECTED = 1;

    /**
     * @var string
     */
    public const STATUS_REPORTED = 0;

    /**
     * @var array
     */
    protected $fillable = [
        'bill_id',
        'payment_id',
        'status',
        'amount',
        'refunded',
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
        'status' => self::STATUS_REPORTED,
    ];

    /**
     * @var array
     */
    private static $statusName = [
        self::STATUS_REPORTED => 'Reported',
        self::STATUS_REJECTED => 'Rejected',
        self::STATUS_TO_REFUNDED => 'To refund',
        self::STATUS_REFUNDED => 'Refunded',
        self::STATUS_ERROR => 'Error',
    ];

    /**
     * @return BelongsTo
     */
    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class, 'bill_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'id');
    }

    public function getStatusName()
    {
        return self::$statusName[$this->status];
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
        $query = self::with(['bill', 'payment'])->limit($limit)->offset($offset);

        if (! empty($criteria['id'])) {
            $query->whereIn('id', $criteria['id']);
        }
        if (! empty($criteria['bill'])) {
            $query->whereIn('bill_id', $criteria['bill']);
        }
        if (! empty($criteria['payment'])) {
            $query->whereIn('payment_id', $criteria['payment']);
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
     *
     * @return LengthAwarePaginator
     */
    public static function getPaginatedForPanel(string $filter = null, int $paginateSize, array $order = null, array $filter_columns = null): LengthAwarePaginator
    {
        $query = self::distinct()->with(['bill', 'payment']);

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
            $query->orWhere('payment_id', 'LIKE', '%'.$filter.'%');
            $query->orWhere('amount', 'LIKE', '%'.$filter.'%');
            $query->orWhere('status', 'LIKE', '%'.$filter.'%');
        }

        return $query->paginate($paginateSize, ['refunds.*']);
    }

    /**
     * @return bool
     */
    public function isUnlockRefund()
    {
        $user = Auth::user();

        return $user->isOne([User::ROLE_ADMIN, User::ROLE_MANAGER]) && ! $this->refunded && $this->status == self::STATUS_ERROR;
    }
}
