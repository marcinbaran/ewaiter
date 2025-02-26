<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="UserTenant",
 *     type="object",
 *     title="UserTenant",
 *     description="User tenant details"
 * )
 */
class UserTenantResource extends JsonResource
{
    use ResourceTrait;

    /**
     * @var int Default limit items per page
     */
    public const LIMIT = 20;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     *
     * @OA\Property(property="id", type="integer", description="The unique identifier of the user tenant"),
     * @OA\Property(property="login", type="string", description="The login name of the user tenant"),
     * @OA\Property(property="email", type="string", description="The email address of the user tenant"),
     * @OA\Property(property="phone", type="string", description="The phone number of the user tenant"),
     * @OA\Property(property="roles", type="array", @OA\Items(type="string"), description="Roles assigned to the user tenant"),
     * @OA\Property(property="fullName", type="string", description="The full name of the user tenant"),
     * @OA\Property(property="address", type="string", description="The address of the user tenant"),
     * @OA\Property(property="points", type="integer", description="The points accumulated by the user tenant"),
     * @OA\Property(property="referredUserId", type="integer", description="The ID of the referred user"),
     * @OA\Property(property="walletBalance", type="string", description="The wallet balance of the user tenant"),
     * @OA\Property(property="activated", type="boolean", description="Indicates if the user tenant is activated"),
     * @OA\Property(property="resId", type="integer", description="The restaurant ID associated with the user tenant"),
     * @OA\Property(property="birth_date", type="string", format="date", description="The birth date of the user tenant")
     */
    public function toArray($request): array
    {
        $array = [
            'id' => $this->id,
            'login' => $this->login,
            'email' => $this->email,
            'phone' => $this->phone,
            'roles' => $this->roles,
            'fullName' => $this->fullName(),
            'address' => $this->address,
            'points' => $this->points,
            'referredUserId' => $this->referred_user_id,
            'walletBalance' => number_format($this->getBalance(), 2, '.', ''),
            'activated' => $this->activated,
            'resId' => $this->res_id,
            'birth_date' => $this->birth_date,
        ];

        return $array;
    }

    /**
     * @return string
     */
    public function fullName(): string
    {
        return implode(' ', array_filter([$this->first_name, $this->last_name]));
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public static function isDevMaster(User $user = null): bool
    {
        $devMail = app('config')['app']['dev_mail'];

        return ! empty($devMail) && $devMail == ($user ? $user->email : auth()->user()->email);
    }
}
