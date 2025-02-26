<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReklamyOutcome extends Model
{
    use ModelTrait;

    protected $connection = 'reklamy';

    protected $table = 'outcomes';

    /**
     * @return HasOne
     */
    public function qr_code(): HasOne
    {
        return $this->belongsTo(ReklamyWallet::class, 'referring_user_id', 'id');
    }
}
