<?php

namespace App\DTO;

use App\Http\Resources\Api\UserResource;
use App\Http\Resources\Api\UserTenantResource;
use App\Models\User;
use App\Models\UserSystem;
use Hyn\Tenancy\Facades\TenancyFacade;

class LoginResponse
{
    public string $token_type;

    public string $expires_in;

    public string $access_token;

    public string $refresh_token;

    public User|UserSystem $user;

    public string $locale;

    public function __construct(
        string $token_type,
        string $expires_in,
        string $access_token,
        string $refresh_token,
        User|UserSystem $user,
        string $locale = null
    ) {
        $this->token_type = $token_type;
        $this->expires_in = $expires_in;
        $this->access_token = $access_token;
        $this->refresh_token = $refresh_token;
        $this->user = $user;
        $this->locale = $locale ?? app()->getLocale();
    }

    public function toArray()
    {
        return [
            'token_type' => $this->token_type,
            'expires_in' => (int) $this->expires_in,
            'access_token' => $this->access_token,
            'refresh_token' => $this->refresh_token,
            'user' => TenancyFacade::website() ? new UserTenantResource($this->user) : new UserResource($this->user),
            'locale' => $this->locale,
        ];
    }
}
