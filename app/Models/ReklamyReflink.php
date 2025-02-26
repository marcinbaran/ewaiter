<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReklamyReflink extends Model
{
    use ModelTrait;

    protected $connection = 'reklamy';

    protected $table = 'reflinks';

    protected $fillable = [
        'system',
        'referring_user_id',
        'object_type',
        'object_id',
        'object_text',
    ];

    /**
     * @var string
     */
    public const TYPE_BILL = 'bill_id';

    /**
     * @var string
     */
    public const TYPE_BILL_POINTS = 'bill_points';

    /**
     * @var string
     */
    public const TYPE_VOUCHER = 'voucher';

    /**
     * @var string
     */
    public const TYPE_MANUALLY = 'manual_change_by_user_id';

    /**
     * @var string
     */
    public const TYPE_REFUND = 'refund';

    /**
     * @var string
     */
    public const TYPE_REFERRING = 'user_id';

    protected static $typeName = [
        self::TYPE_BILL => 'Cashback',
        self::TYPE_BILL_POINTS => 'Order',
        self::TYPE_VOUCHER => 'Voucher',
        self::TYPE_MANUALLY => 'Modified manually',
        self::TYPE_REFUND => 'Refund',
        self::TYPE_REFERRING => 'Reflink',
    ];

    public static function getTypeName(string $type)
    {
        return isset(self::$typeName[$type]) ? self::$typeName[$type] : $type;
    }

    /**
     * @return HasOne
     */
    public function income(): HasOne
    {
        return $this->hasOne(ReklamyIncome::class, 'reflink_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function outcome(): HasOne
    {
        return $this->hasOne(ReklamyOutcome::class, 'reflink_id', 'id');
    }

    public static function isRegistered(int $referringUserId): bool
    {
        return self::where('referring_user_id', $referringUserId)->where('object_type', 'registration')->count() > 0;
    }
}
