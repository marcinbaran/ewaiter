<?php

namespace App\View\Components\Admin\Layout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SidebarListItem extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $route,
        public string $iconPath,
        public string $label,
        public bool $isActive
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.layout.sidebar-list-item');
    }
}
