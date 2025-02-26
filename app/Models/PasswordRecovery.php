<?php

namespace App\Models;

use App\Managers\PasswordRecoveryManager;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PasswordRecovery extends Model
{
    private $manager;

    public function __construct()
    {
        parent::__construct();
        $this->manager = new PasswordRecoveryManager();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'code',
        'attempts',
        'used',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public static function getLatestPasswordRecoveryData(User|UserSystem $user)
    {
        return self::where('user_id', $user->id)->get()->last();
    }

    public function isRecoveryCodeInvalid(): bool
    {
        return ! $this->isExpired() && ($this->isUsed() || $this->isSentThrice());
    }

    public function isExpired(): bool
    {
        return Carbon::now()->diffInMinutes(Carbon::parse($this->created_at)) > config('password-recovery.PASSWORD_RECOVERY_TTL_MINUTES');
    }

    public function isUsed(): bool
    {
        return $this->used;
    }

    public function isSentThrice(): bool
    {
        return $this->attempts >= config('password-recovery.PASSWORD_SMS_CODE_ATTEMPTS');
    }
}
