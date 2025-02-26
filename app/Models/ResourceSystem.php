<?php

namespace App\Models;

use App\Http\Resources\PhotoTrait;
use Hyn\Tenancy\Traits\UsesSystemConnection;
use Illuminate\Database\Eloquent\Model;

class ResourceSystem extends Model
{
    use ModelTrait;
    use PhotoTrait;
    use UsesSystemConnection;

    protected $table = 'resources';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'filename',
        'mime_type',
        'additional'
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'resourcetable_id',
        'resourcetable_type',
    ];

    protected $casts = [
        'additional' => 'array',
    ];

    /**
     * Get all of the owning resourcetable models.
     */
    public function resourcetable()
    {
        return $this->morphTo();
    }
}
