<?php

namespace App\View\Components\Admin\Settings\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Logo extends Component
{
    public $user_roles;

    public function __construct(
        public int $isWebsite = 0,
    )
    {
        $this->user_roles = Auth::user()->roles;

    }

    public function render(): View|Closure|string
    {
        return view('components.admin.settings.forms.logo', ['user_roles' => $this->user_roles, 'options' => config('options_visibility.logo')]);
    }
}

