<?php

namespace App\Models;

use App\Http\Resources\PhotoTrait;
use App\Services\UploadService;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use ModelTrait, PhotoTrait;
    use UsesTenantConnection;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'filename',
        'mime_type',
        'additional',
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
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function resourcetable()
    {
        return $this->morphTo();
    }

    public function getFilePath(bool $public = false)
    {
        return public_path((new UploadService())->getPublicFolder($this->resourcetable_type, $this->resourcetable_id, $public).'/'.$this->filename);
    }

    public function getFilePathAttribute()
    {
        return $this->getFilePath(true);
    }

    public function getFileUrl(): string
    {
        return env('APP_URL').(new UploadService())->getPublicFolder($this->resourcetable_type, $this->resourcetable_id, true).'/'.$this->filename;
    }
}
