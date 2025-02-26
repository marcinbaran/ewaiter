<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class LogController extends Controller
{
    public const PAGINATE_SIZE = 10;

    /**
     * index function.
     *
     * @author Łukasz Polak <lpolak@primebitstudio.com>
     **/
    public function index()
    {
        return view('admin.logs.index')->with([
            'controller' => 'logs',
        ]);
    }
}
