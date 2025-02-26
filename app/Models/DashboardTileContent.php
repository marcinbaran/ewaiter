<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DashboardTileContent extends Model
{
    protected $guarded = [];

    protected $hidden = [
    'content_type',
    ];

    /**
     * @return BelongsTo
     */
    public function dashboardTile(): BelongsTo
    {
        return $this->belongsTo(DashboardTile::class, 'dashboard_tile_id', 'ID');
    }

    /**
     * @return BelongsTo
     */
    public function dashboardTileContentType(): BelongsTo
    {
        return $this->belongsTo(DashboardTileContentType::class, 'content_type', 'ID');
    }
}
