<?php

namespace App\Http\Middleware;

use App\Models\ApiLog;
use Closure;
use Illuminate\Http\Request;

class WebApiLog
{
    /**
     * Handle an incoming request.
     *
     * @param Request  $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        ApiLog::log();

        return $next($request);
    }
}
