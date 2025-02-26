<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class OauthClient extends Model
{
    use ModelTrait;
    use UsesTenantConnection;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'oauth_clients';

    /**
     * @var array
     */
    protected $hidden = [
        'secret',
        'updated_at',
        'created_at',
    ];
}
