<?php

namespace App\Models;

use App\Models\Traits\ModifyPoints;
use Hyn\Tenancy\Traits\UsesSystemConnection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Expression;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

/**
 * Class UserSystem.
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $email
 * @property string|null $password
 * @property int $blocked
 * @property string|null $phone
 * @property string|null $login
 * @property string|null $roles
 * @property int $guest
 * @property string|null $auth_code
 * @property int|null $referred_user_id
 * @property string|null $reflink_date
 * @property int|null $res_id
 * @property int $activated
 * @property string|null $remember_token
 * @property string|null $last_login
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $auth_type
 * @property string|null $external_auth_id
 */
class UserSystem extends Authenticatable
{
    use HasApiTokens,
        Notifiable,
        ModelTrait,
        RoleHasTrait,
        UsesSystemConnection,
        ModifyPoints;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $table = 'users';

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
     * @var array
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
        'auth_code',
        'referred_user_id',
        'reflink_date',
        'res_id',
        'activated',
        'auth_code',
        'auth_type',
        'external_auth_id',
        'birth_date',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'activated',
        'notification_id',
        'regulations',
        'remember_token',
        'last_login',
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'guest' => 0,
        'activated' => 0,
        'regulations' => 1,
        'blocked' => 0,
        'roles' => 'test',
        //'[' . self::ROLE_USER . ']',
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
     * @return HasMany
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddressSystem::class, 'user_id', 'id');
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
     * @param string $email
     *
     * @return User|null
     */
    public static function findAccountToRemind(string $email)
    {
        return self::where('email', $email)->first();
    }

    /**
     * @param string $login
     *
     * @return User|null
     */
    public static function findActivatedByLogin(string $login)
    {
        return self::with('playerIds:id,player_id')
            ->where(function ($q) use ($login) {
                $q->where('login', $login)
                    ->orWhere('email', $login);
            })
            ->first();
    }

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

        if (! empty($criteria['user'])) {
            $query->whereIn('id', $criteria['user']);
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
        $query = self::select('users.*')
            ->where('email', 'like', '%@%');

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

    public function getBalance()
    {
        if ($this->reklamy_referring_user && $this->reklamy_referring_user->wallet) {
            return $this->reklamy_referring_user->wallet->balance ?? 0.0;
        }

        return 0.0;

        //return 0.00;
        // if (config('app.curl_test')) {
        //     $http = new \GuzzleHttp\Client([
        //         'curl' => array(CURLOPT_SSL_VERIFYPEER => false),
        //         'verify' => false,
        //     ]);
        // } else {
        //     $http = new GuzzleHttp\Client;
        // }
        // $api_url = config('admanager.url');
        // $response = $http->request('POST', $api_url . '/auth', [
        //     'body' => json_encode([
        //         'login' => 'wirtualnykelner',
        //         'password' => 'zetoapp'
        //     ]),
        //     'headers' => [
        //         'Content-Type' => 'application/json',
        //     ]
        // ]);
        // $body = json_decode((string) $response->getBody(), true);
        // if ($response->getStatusCode() == 200 && !empty($body['access_token']))
        //     $access_token = $body['access_token'];
        // else
        //     return 0.00;

        // $response = $http->request('GET', $api_url . '/referring_users', [
        //     'body' => json_encode([
        //         'email' => $this->email
        //     ]),
        //     'headers' => [
        //         'Authorization' => "Bearer {$access_token}",
        //         'Content-Type' => 'application/json',
        //     ]
        // ]);
        // $body = json_decode((string) $response->getBody(), true);
        // if ($response->getStatusCode() == 200 && !empty($body['data'][0]['walletBalance']))
        //     return $body['data'][0]['walletBalance'];
        // else
        //     return 0.00;
    }

    public function getRolesAttribute($value)
    {
        return is_array($value) ? $value : json_decode($value, true);
    }

    public function setRolesAttribute(array $value)
    {
        $this->attributes['roles'] = json_encode($value);
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
}
