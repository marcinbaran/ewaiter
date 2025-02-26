<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnlinePaymentProviderAccount extends Model
{
    use ModelTrait;

    protected $table = 'online_payment_provider_accounts';

    protected $fillable = [
        'comment',
        'login',
        'password',
        'api_key',
        'api_password',
        'restaurant_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
