<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\ResourceTrait;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    use ResourceTrait;

    /**
     * @var int Default limit items per page
     */
    public const LIMIT = 20;

    public function __get($key)
    {
        $val = parent::__get($key);

        switch ($key) {
            case 'table': return new TableResource($val);
                break;
            case 'playerIds': return PlayerIdResource::collection($val);
                break;
            case 'isRoom': return $this->hasRole(User::ROLE_ROOM);
                break;
            default: return $val;
                break;
        }
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array
     */
    public function toArray($request)
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
            //'walletBalance' => number_format($this->getBalance(), 2, '.', ''),
            'resId' => $this->res_id,
        ];
        $array['isRoom'] = $this->hasRole(User::ROLE_ROOM);
        if ($this->hasRoles([User::ROLE_TABLE, User::ROLE_USER])) {
            $array['table'] = $this->table()->first()?->resource ? ['id' => $this->table()->first()?->id] : [];
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
