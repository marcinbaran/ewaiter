<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Pagination\LengthAwarePaginator;

class PlayerId extends Model
{
    use ModelTrait;
    use UsesTenantConnection;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'player_id',
        'device_info',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class, 'user_id', 'id');
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
        $query = self::with('user')->limit($limit)->offset($offset);

        if (! empty($criteria['id'])) {
            $query->whereIn('player_ids.id', $criteria['id']);
        }
        if (! empty($criteria['user'])) {
            $query->whereIn('player_ids.user_id', $criteria['user']);
        }
        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                $query->orderBy('player_ids.'.self::decamelize($column), $direction);
            }
        }

        return $query->get();
    }

    /**
     * @param array $roles
     *
     * @return Collection
     */
    public static function findDevicesByRoles(array $roles): Collection
    {
        if (empty($roles)) {
            return new Collection();
        }
        $query = self::join('users', 'users.id', '=', 'player_ids.user_id');
        foreach ($roles as $role) {
            $query->orWhere('users.roles', 'LIKE', '%'.$role.'%');
        }

        return $query->get();
    }

    /**
     * @param string|null $filter
     * @param int         $paginateSize
     *
     * @return LengthAwarePaginator
     */
    public static function getPaginatedForPanel(string $filter = null, int $paginateSize, array $criteria = []): LengthAwarePaginator
    {
        $query = self::orderBy('player_id', 'asc')->with('user:id,first_name,last_name');

        if (! empty($filter)) {
            $query->where('player_id', 'LIKE', '%'.$filter.'%');
        }
        if (! empty($criteria['user'])) {
            $query->whereIn('user_id', $criteria['user']);
        }
        if (! empty($criteria['id'])) {
            $query->whereIn('id', $criteria['id']);
        }

        return $query->paginate($paginateSize);
    }

    /**
     * @param int|string $playerId
     * @param User       $user
     *
     * @return PlayerId|null
     */
    public static function findPlayerIdForUser($playerId, User $user)
    {
        return self::where('id', $playerId)
            ->orWhere(function ($query) use ($playerId, $user) {
                $query->where('player_id', $playerId)
                    ->where('user_id', $user->id);
            })->first();
    }
}
