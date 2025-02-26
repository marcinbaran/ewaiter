<?php

namespace App\View\Components\Admin\Layout;

use Closure;
use Hyn\Tenancy\Facades\TenancyFacade;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NavBar extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public int $isWebsite = 0)
    {
        $this->isWebsite = TenancyFacade::website() ? 1 : 0;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.layout.nav-bar', [
                'isWebsite' => $this->isWebsite,
            ]);
    }
}
