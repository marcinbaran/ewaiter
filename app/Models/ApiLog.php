<?php

namespace App\Models;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    use UsesTenantConnection;

    public $fillable = [
        'user_id',
        'request_body',
        'full_url',
        'method',
    ];

    /**
     * log function.
     *
     * @author Łukasz Polak <lpolak@primebitstudio.com>
     **/
    public static function log()
    {
        $user_id = null;

        if (auth()->check()) {
            $user_id = auth()->user()->id;
        }

        self::create([
            'user_id' => $user_id,
            'method' => request()->method(),
            'full_url' => request()->fullUrl(),
            'request_body' => strpos(request()->getContent(), 'password') ? '' : request()->getContent(),
        ]);
    }

    /**
     * getPaginatedForPanel function.
     *
     * @author Łukasz Polak <lpolak@primebitstudio.com>
     **/
    public static function getPaginatedForPanel($filter, $paginate_size)
    {
        $query = self::orderBy('id', 'desc');

        if (! empty($filter)) {
            $query->where('full_url', 'LIKE', '%'.$filter.'%');
            $query->orWhere('METHOD', 'LIKE', '%'.$filter.'%');
            $query->orWhere('user_id', $filter);
        }

        return $query->paginate($paginate_size);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
