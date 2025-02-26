<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReklamyReferringUser extends Model
{
    use ModelTrait;

    protected $connection = 'reklamy';

    protected $table = 'referrings_users';

    /**
     * @return HasOne
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(ReklamyWallet::class, 'referring_user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function reflinks(): HasMany
    {
        return $this->hasMany(ReklamyReflink::class, 'referring_user_id', 'id')->where('system', 'like', '%wirtualnykelner%')->where(function ($q) {
            $q->has('income')
                ->orHas('outcome');
        });
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class, 'referring_user_id', 'id');
    }
}
