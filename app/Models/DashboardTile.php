<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DashboardTile extends Model
{
    //
    use ModelTrait;

    protected $guarded = [];

    protected $hidden = [
    'type',
    ];

    /**
     * @return HasMany
     */
    public function dashboardTileContent(): HasMany
    {
        return $this->hasMany(DashboardTileContent::class, 'dashboard_tile_id', 'ID')->with('dashboardTileContentType');
    }

    /**
     * @return BelongsTo
     */
    public function dashboardTileType(): BelongsTo
    {
        return $this->belongsTo(DashboardTileType::class, 'type', 'ID');
    }

    public static function getRows()
    {
        $query = self::select()->with('dashboardTileType')->with('dashboardTileContent');

        return $query->get();
    }
}
