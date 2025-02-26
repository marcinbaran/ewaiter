<?php

namespace App\Models;

use App\Models\Traits\ModifyPoints;
use App\Services\GlobalSearch\Searchable;
use App\Services\ReferringUserService;
use Carbon\Carbon;
use Hyn\Tenancy\Facades\TenancyFacade;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Expression;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Passport\HasApiTokens;
/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     required={"id", "login", "email"},
 *     @OA\Property(property="id", type="integer", description="ID of the user"),
 *     @OA\Property(property="login", type="string", description="Login of the user"),
 *     @OA\Property(property="email", type="string", description="Email of the user"),
 *     @OA\Property(property="phone", type="string", nullable=true, description="Phone number of the user"),
 *     @OA\Property(property="roles", type="array", @OA\Items(type="string"), description="Roles of the user"),
 *     @OA\Property(property="fullName", type="string", nullable=true, description="Full name of the user"),
 *     @OA\Property(property="referredUserId", type="integer", nullable=true, description="ID of the referred user"),
 *     @OA\Property(property="walletBalance", type="string", description="Wallet balance of the user"),
 *     @OA\Property(property="pointsRatio", type="integer", description="Points ratio of the user"),
 *     @OA\Property(property="activated", type="boolean", description="Is the user activated"),
 *     @OA\Property(property="auth_type", type="string", nullable=true, description="Authentication type"),
 *     @OA\Property(property="resId", type="integer", nullable=true, description="Reservation ID"),
 *     @OA\Property(property="birth_date", type="string", format="date", nullable=true, description="Birth date of the user"),
 *     @OA\Property(property="isTester", type="boolean", description="Is the user a tester"),
 *     @OA\Property(property="addresses", type="array", @OA\Items(ref="#/components/schemas/Address"), description="List of user's addresses"),
 * )
 */
class User extends Authenticatable implements Searchable
{
    use HasApiTokens,
        HasFactory,
        HasProfilePhoto,
        Notifiable,
        ModelTrait,
        RoleHasTrait,
        TwoFactorAuthenticatable,
        UsesTenantConnection,
        ModifyPoints;

    //simple rooles witout hierarchy
    public const ROLE_ROOM = 'ROLE_ROOM';

    public const ROLE_TABLE = 'ROLE_TABLE';

    public const ROLE_MANAGER = 'ROLE_MANAGER';

    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public const ROLE_USER = 'ROLE_USER';

    public const ROLE_GUEST = 'ROLE_GUEST';

    public const ROLE_WAITER = 'ROLE_WAITER';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'blocked',
        'phone',
        'login',
        'roles',
        'guest',
        'referred_user_id',
        'res_id',
        'activated',
        'auth_code',
        'birth_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'activated',
        'auth_code',
        'notification_id',
        'regulations',
        'remember_token',
        'last_login',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'guest' => 0,
        'activated' => 1,
        'regulations' => 1,
        'blocked' => 0,
        'roles' => 'test', //[self::ROLE_USER],
    ];

    /**
     * @return HasOne
     */
    public function reklamy_referring_user(): HasOne
    {
        return $this->hasOne(ReklamyReferringUser::class, 'email', 'email');
    }

    /**
     * @return HasOne
     */
    public function table(): HasOne
    {
        return $this->hasOne(Table::class, 'user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function playerIds(): HasMany
    {
        return $this->hasMany(PlayerId::class, 'user_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function address(): HasOne
    {
        return $this->hasOne(Address::class, 'id', 'address_id');
    }

    public function passwordRecoveries(): HasMany
    {
        return $this->hasMany(PasswprdRecovery::class, 'id', 'user_id');
    }

    /**
     * @param string $login
     *
     * @return User|null
     */
    public function findForPassport(string $login)
    {
        return $this->where('login', $login)->first();
    }

    /**
     * @param string $login
     *
     * @return User|null
     */
    public static function findActivatedByLogin(string $login)
    {
        return self::with('playerIds:id,player_id')->where('login', $login)->where('activated', 1)->first();
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * @param string $login
     *
     * @return User|null
     */
    public static function findGuest(string $login)
    {
        return self::where('login', $login)->where('guest', 1)->first();
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
        $query = self::limit($limit)->offset($offset);

        if (! empty($criteria['id'])) {
            $query->whereIn('id', $criteria['id']);
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
        /** @var Builder $query */
        $query = self::select(['users.*']);

        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                'name' == $column ?
                    $query->orderBy(new Expression('CONCAT(`first_name`, `last_name`)'), $direction) :
                    $query->orderBy(self::decamelize($column), $direction);
            }
        }
        if (! empty($filter)) {
            $query->where('first_name', 'LIKE', '%'.$filter.'%');
            $query->orWhere('last_name', 'LIKE', '%'.$filter.'%');
            $query->orWhere('email', 'LIKE', '%'.$filter.'%');
            $query->orWhere('login', 'LIKE', '%'.$filter.'%');
        }

        $query->where(function ($q) use ($filter) {
            $q->where('guest', 0)
                ->orWhere('guest', 1)
                ->where('created_at', '>', Carbon::now()->subWeeks(2));
        });

        return $query->paginate($paginateSize, ['users.id']);
    }

    /**
     * @return LengthAwarePaginator
     */
    public static function getMyData(): LengthAwarePaginator
    {
        $query = self::where('id', auth()->user()->id);

        return $query->paginate(1);
    }

    public static function getPoints($user, $request)
    {
        $stats = [];
        $criteria = [];
        $createdAt = $request->query->get('createdAt');
        if (! empty($createdAt['start'])) {
            $criteria[] = ['created_at', '>=', $createdAt['start']];
        }
        if (! empty($createdAt['end'])) {
            $criteria[] = ['created_at', '<=', $createdAt['end']];
        }

        $reflinks = ! empty($criteria) ? $user->reklamy_referring_user->reflinks()->where($criteria)->get() : $user->reklamy_referring_user->reflinks;

        if (count($reflinks)) {
            foreach ($reflinks as $reflink) {
                $reflink_data = [];
                $reflink_data['created_at'] = Carbon::parse($reflink->created_at)->format('Y-m-d H:i:s');
                $reflink_data['system'] = $reflink->system;
                $reflink_data['object_type'] = __('admin.'.ReklamyReflink::getTypeName($reflink->object_type));
                $reflink_data['object_id'] = $reflink->object_id;
                $reflink_data['income'] = $reflink->income ? $reflink->income->cost : null;
                $reflink_data['outcome'] = $reflink->outcome ? $reflink->outcome->cost : null;
                $stats['result'][] = $reflink_data;
            }
        }
        $stats['columns'] = [
            __('admin.Created at'),
            __('admin.URL'),
            __('admin.Type'),
            __('admin.ID'),
            __('admin.Income'),
            __('admin.Outcome'),
        ];

        return collect($stats);
    }

    public static function send_points($params)
    {
        $sender = self::find($params['sender_id']);

        $result['success'] = false;
        $result['balance'] = $sender ? $sender->getBalance() : -1;

        $amount = $params['amount'];
        if (! empty($params['receiver_id'])) {
            $receiver = self::find($params['receiver_id']);
        } elseif (! empty($params['receiver_phone'])) {
            $receiver = self::where('phone', $params['receiver_phone'])->first();
        } elseif (! empty($params['receiver_email'])) {
            $receiver = self::where('email', $params['receiver_email'])->first();
        }

        if ($sender && $receiver) {
            if ($sender->id == $receiver->id) {
                return $result;
            }
            if (! empty($sender) && ! empty($receiver) && ! empty($amount) && $amount > 0) {
                if ($sender->getBalance() >= $amount) {
                    if ($sender->modify_points(-$amount)) {
                        if ($receiver->modify_points($amount)) {
                            $result['success'] = true;
                            $result['balance'] = round($sender->getBalance());

                            return $result;
                        }
                    }
                }

                return $result;
            }
        } else {
            return $result;
        }
    }

    public function getBalance()
    {
        try {
            return (new ReferringUserService())->getReferringUser($this)->wallet->balance;
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    public function setRolesAttribute(/*array*/$value)
    {
        $this->attributes['roles'] = json_encode($value);
    }

    public function getRolesAttribute($value)
    {
        return is_array($value) ? $value : json_decode($value, true);
    }

    public static function setNewPassword(string $newPassword, self $user)
    {
        $user->password = bcrypt($newPassword);

        return $user->update();
    }

    public static function findForPhrase(string $phrase = ''): Collection
    {
        if (TenancyFacade::website()) {
            return new Collection([]);
        }

        return self::query()
            ->where(DB::raw('CONCAT(first_name, " ", last_name)'), 'like', '%'.$phrase.'%')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function getSearchGroupName(): string
    {
        return __('admin.Users');
    }

    public function getSearchUrl(): string
    {
        return route('admin.users.edit', ['user' => $this->id]);
    }

    public function getSearchTitle(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function getSearchDescription(): string
    {
        return $this->email.', '.$this->phone;
    }

    public function getSearchPhoto(): string
    {
        return '';
    }

    /**
     * Validate the password of the user for the Passport password grant.
     *
     * @param  string  $password
     * @return bool
     */
    public function validateForPassportPasswordGrant($password)
    {
        return Hash::check($password, $this->password);
    }

    public function review(): HasMany
    {
        return $this->hasMany(Review::class, 'user_id', 'id');
    }

    public function getNameOrEmail(): string
    {
        return ($this->first_name) ? $this->first_name : $this->email;
    }
}
