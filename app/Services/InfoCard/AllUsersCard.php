<?php

namespace App\Services\InfoCard;

use App\Models\User;
use Carbon\Carbon;

class AllUsersCard extends AbstractCard
{
    private ?Carbon $start;

    private ?Carbon $end;

    /**
     * @param \Carbon\Carbon|null $start
     * @param \Carbon\Carbon|null $end
     */
    public function __construct(?Carbon $start = null, ?Carbon $end = null)
    {
        $this->start = $start;
        $this->end = $end;

        $this->title = __('admin.Users count');
        $this->color = 'danger';
        $this->icon = 'database';
    }

    public function setValue()
    {
        if ($this->start && $this->end) {
            $this->value = User::query()
//                ->whereBetween('created_at', [$this->start, $this->end])
                ->count();
        }
    }
}
