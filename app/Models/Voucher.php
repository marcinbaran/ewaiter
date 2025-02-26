<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voucher extends Model
{
    use ModelTrait;

    protected $table = 'vouchers';

    protected $fillable = [
        'code',
        'comment',
        'value',
        'is_used',
        'used_at',
        'used_by',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'is_used' => 0,
        'used_at' => null,
        'used_by' => null,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by');
    }
}
