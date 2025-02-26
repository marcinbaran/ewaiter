<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesSystemConnection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
/**
 * @OA\Schema(
 *     schema="AddressSystem",
 *     type="object",
 *     @OA\Property(property="company_name", type="string", description="Company name", example="Example Inc."),
 *     @OA\Property(property="nip", type="string", description="NIP (tax identification number)", example="1234567890"),
 *     @OA\Property(property="name", type="string", description="First name", example="John"),
 *     @OA\Property(property="surname", type="string", description="Last name", example="Doe"),
 *     @OA\Property(property="city", type="string", description="City", example="Warsaw"),
 *     @OA\Property(property="postcode", type="string", description="Postal code", example="00-001"),
 *     @OA\Property(property="street", type="string", description="Street", example="Main Street"),
 *     @OA\Property(property="building_number", type="string", description="Building number", example="10"),
 *     @OA\Property(property="house_number", type="string", description="House number", example="5A"),
 *     @OA\Property(property="is_default", type="boolean", description="Is default address", example=true),
 *     @OA\Property(property="floor", type="string", description="Floor", example="3"),
 *     @OA\Property(property="phone", type="string", description="Phone number", example="+48123456789"),
 *     @OA\Property(property="lat", type="number", format="float", description="Latitude", example=52.2297),
 *     @OA\Property(property="lng", type="number", format="float", description="Longitude", example=21.0122),
 *     @OA\Property(property="radius", type="number", format="float", description="Radius", example=100.0),
 * )
 */
class AddressSystem extends Model
{
    use ModelTrait;
    use UsesSystemConnection;

    protected $table = 'addresses';

    /**
     * @var array
     */
    protected $fillable = [
        'company_name',
        'nip',
        'name',
        'surname',
        'city',
        'postcode',
        'street',
        'building_number',
        'house_number',
        'is_default',
        'floor',
        'phone',
        'lat',
        'lng',
        'radius',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    /**
     * @return HasMany
     */
    public function user_addresses(): HasMany
    {
        return $this->hasMany(UserAddressSystem::class, 'address_id', 'id');
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
            $query->whereHas('user_addresses', function ($q) use ($criteria) {
                $q->whereIn('user_id', $criteria['user']);
            });
        }

        if (! empty($order)) {
            foreach ($order as $column => $direction) {
                $query->orderBy(self::decamelize($column), $direction);
            }
        }

        return $query->get();
    }

    public function isUserAddress()
    {
        $user_logged = Auth::user();
        $address_user = self::whereHas('user_addresses', function ($q) use ($user_logged) {
            $q->where('user_id', $user_logged->id);
        })->where('id', $this->id)->first();

        return (bool) $address_user;
    }
}
