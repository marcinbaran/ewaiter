<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\NotificationResource;

class NotificationController extends Controller
{
    public function __construct()
    {
        NotificationResource::wrap('results');
    }
}
