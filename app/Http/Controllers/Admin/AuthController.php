<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SessionRequest;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Request;

class AuthController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|RedirectResponse
     */
    public function create()
    {
        if (auth()->check()) {
            return redirect()->route('admin.dashboard.index');
        }

        return view('admin.auth.login');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SessionRequest $request
     *
     * @return RedirectResponse
     */
    public function store(SessionRequest $request)
    {
        // Attempt to authenticate user
        $user = User::where('email', $request->get('email'))->first();
        if ($user && $user->blocked) {
            $request->session()->flash('alert-warning', __('admin.Account blocked'));

            return redirect()->route('admin.auth.login');
        }
        if (! auth()->attempt($request->only(['email', 'password']))) {
            $request->session()->flash('alert-warning', __('invalid.creditentials'));

            return redirect()->route('admin.auth.login');
        }

        // Redirect
        return redirect()->route('admin.dashboard.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return RedirectResponse
     */
    public function destroy()
    {
        auth()->logout();

        return redirect()->route('admin.auth.login');
    }

    public function login_admin($token, Request $request)
    {
        $user = User::where('remember_token', $token)->first();

        // Attempt to authenticate user
        if ($user) {
            $user->remember_token = null;
            $user->save();

            auth()->loginUsingId($user->id);

            return redirect()->route('admin.dashboard.index');
        }

        session()->flash('alert-warning', __('invalid.creditentials'));

        return redirect()->route('admin.auth.login');
    }

    public function verifyUser($lang, $token)
    {
        \App::setLocale($lang);
        $user = User::where('auth_code', $token)->first();
        if (isset($user)) {
            if (! $user->activated) {
                $user->activated = 1;
                $user->save();
                $status = __('emails.Your e-mail is verified. You can now login in the app.');
            } else {
                $status = __('emails.Your e-mail is already verified. You can now login in the app.');
            }
        } else {
            $status = __('emails.Sorry your email cannot be identified.');
        }

        echo $status;
        exit;
    }
}
