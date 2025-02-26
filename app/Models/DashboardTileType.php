<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DashboardTileType extends Model
{
    protected $guarded = [];

    /**
     * @return HasMany
     */
    public function dashboardTile(): HasMany
    {
        return $this->hasMany(DashboardTile::class, 'type', 'ID');
    }
}
