<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReklamyIncome extends Model
{
    use ModelTrait;

    protected $connection = 'reklamy';

    protected $table = 'incomes';

    protected $fillable = [
        'reflink_id',
        'cost',
    ];

    /**
     * @return HasOne
     */
    public function qr_code(): HasOne
    {
        return $this->belongsTo(ReklamyWallet::class, 'referring_user_id', 'id');
    }
}
