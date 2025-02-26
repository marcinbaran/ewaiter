<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class AdminCheck
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->is('admin/login')) {
            if (!auth()->check()) {
                auth()->logout();

                return
                    redirect()->route('admin.auth.login');
            }

            if (!auth()->user()->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN, User::ROLE_WAITER])) {
                auth()->logout();

                $request->session()->flash('alert-danger', 'Wrong role');

                return redirect()->route('admin.auth.login');
            }
        }

        return $next($request);
    }
}
