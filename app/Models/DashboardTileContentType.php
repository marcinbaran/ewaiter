<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DashboardTileContentType extends Model
{
    protected $guarded = [];

    /**
     * @return HasMany
     */
    public function dashboardTileContent(): HasMany
    {
        return $this->hasMany(DashboardTileContent::class, 'content_type', 'ID');
    }
}
