<?php

namespace App\Http\Middleware;

use Closure;

class LastLogin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();
            $user->last_login = $user->freshTimestamp();
            $user->save();
        }

        return $next($request);
    }
}
