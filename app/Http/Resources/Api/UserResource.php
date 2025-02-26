<?php

namespace App\Http\Resources\Api;

use App\Exceptions\ApiExceptions\General\BadConfigurationException;
use App\Http\Resources\ResourceTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
/**
 * @OA\Schema(
 *     schema="UserResource",
 *     type="object",
 *     title="User Resource",
 *     description="User resource representation",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="login", type="string", example="user_login"),
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="phone", type="string", example="+1234567890"),
 *     @OA\Property(property="roles", type="array", @OA\Items(type="string"), example={"ROLE_USER", "ROLE_ADMIN"}),
 *     @OA\Property(property="fullName", type="string", example="John Doe"),
 *     @OA\Property(property="referredUserId", type="integer", example=2),
 *     @OA\Property(property="walletBalance", type="string", example="100.00"),
 *     @OA\Property(property="pointsRatio", type="integer", example=100),
 *     @OA\Property(property="activated", type="boolean", example=true),
 *     @OA\Property(property="auth_type", type="string", example="standard"),
 *     @OA\Property(property="resId", type="integer", example=3),
 *     @OA\Property(property="birth_date", type="string", format="date", example="1990-01-01"),
 *     @OA\Property(property="isTester", type="boolean", example=false),
 *     @OA\Property(
 *         property="addresses",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/AddressResource"),
 *         description="List of user's addresses"
 *     )
 * )
 */
class UserResource extends JsonResource
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
     */
    public function toArray($request): array
    {
        $pointsRatio = (int) config('admanager.ratio');

        if ($pointsRatio === null && $pointsRatio > 0) {
            throw new BadConfigurationException([BadConfigurationException::MISSING_CONFIG_ENTRY_KEY => 'points ratio']);
        }

        $array = [
            'id' => $this->id,
            'login' => $this->login,
            'email' => $this->email,
            'phone' => $this->phone,
            'roles' => $this->roles,
            'fullName' => $this->fullName(),
            'referredUserId' => $this->referred_user_id,
            'walletBalance' => $this->reklamy_referring_user ? (string) round($this->reklamy_referring_user->wallet->balance) : '0',
            'pointsRatio' => $pointsRatio,
            'activated' => $this->activated,
            'auth_type' => $this->auth_type,
            'resId' => $this->res_id,
            'birth_date' => $this->birth_date,
            'isTester' => $this->is_tester,
        ];
        if ($this->addresses && count($this->addresses)) {
            foreach ($this->addresses as $address) {
                $array['addresses'][] = new AddressResource($address->address);
            }
        } else {
            $array['addresses'] = [];
        }

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
