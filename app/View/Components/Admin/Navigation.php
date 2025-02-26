<?php

namespace App\View\Components\Admin;

use App\Models\Settings;
use Closure;
use Hyn\Tenancy\Facades\TenancyFacade;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Router;
use Illuminate\View\Component;

class Navigation extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(private Router $router, public $isWebsite = 0)
    {
        $this->isWebsite = TenancyFacade::website() ? 1 : 0;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.navigation', $this->getAttributes());
    }

    private function getAttributes()
    {
        $breadcrumbs = [];
        $prev = '';

        if ($this->router->current()->getName() == 'admin.dashboard.index') {
            return [
                'title' => 'dashboard',
                'breadcrumbs' => [],
            ];
        }

        foreach (explode('.', $this->router->current()->getName()) as $key => $value) {
            if ($key == 0) {
                $prev = $value;
                continue;
            }
            $breadcrumbs[] = [
                'title' => $value,
                'url' => ($prev == 'admin') ? route('admin.'.$value.'.index') : '',
            ];
            $prev = $value;
        }

        if ($breadcrumbs[0]['title'] == 'delivery_ranges') {
            $breadcrumbs[0]['url'] = route('admin.settings.edit', Settings::where('key', 'konfiguracja_dostawy')->first()->id);
        }

        return [
            'title' => str_replace('.', '_', $this->router->current()->getName()),
            'breadcrumbs' => $breadcrumbs,
        ];
    }
}
