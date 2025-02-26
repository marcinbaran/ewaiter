<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ApiLogController extends Controller
{
    /**
     * index function.
     *
     * @author Łukasz Polak <lpolak@primebitstudio.com>
     **/
    public function index()
    {
        return view('admin.api_logs.index')->with([
            'controller' => 'api_log',
            'action' => 'index',
        ]);
    }
}
