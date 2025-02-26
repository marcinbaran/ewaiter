<?php

namespace App\View\Components\Admin\Layout;

use Closure;
use Hyn\Tenancy\Facades\TenancyFacade;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class QuickActions extends Component
{
    public $user_roles;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public int $isWebsite = 0,
    )
    {
        $this->user_roles = Auth::user()->roles;
        $this->isWebsite = TenancyFacade::website() ? 1 : 0;

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.layout.quick-actions', ['user_roles' => $this->user_roles]);
    }
}
