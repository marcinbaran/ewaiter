<?php

namespace App\Listeners;

use App\ChangeLog;
use App\Events\ChangeLogs;

class SaveLog
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param Change $event
     */
    public function handle(ChangeLogs $event)
    {
        $user = auth()->user();
        $data = [
            'model' => get_class($event->element),
            'action' => $event->action,
            'user_id' => $user ? $user->id : 0,
            'user_name' => $user ? ($user->first_name.' '.auth()->user()->last_name) : 'seed',
            'element_id' => $event->element->id,
            'data' => json_encode(array_diff_key($event->element->toArray(), ['password' => 1])),
        ];

        ChangeLog::create($data);
    }
}
