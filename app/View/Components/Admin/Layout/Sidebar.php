<?php

namespace App\View\Components\Admin\Layout;

use App\Models\Restaurant;
use App\Models\Settings;
use App\Services\UtilService;
use Closure;
use Hyn\Tenancy\Facades\TenancyFacade;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\View\Component;

class Sidebar extends Component
{
    /**
     * Create a new component instance.
     */
    public $user_roles;

    public function __construct(
        public string  $version = '0.0.0',
        public array   $menu = [],
        public         $locales = [],
        public         $currentLanguage = '',
        private bool   $isWebsite = false,
        private string $routeParent = '',
    )
    {
        $website = TenancyFacade::website();
        $this->isWebsite = !is_null($website);
        $this->currentLanguage = app()->getLocale();
        $this->routeParent = $this->getRouteParent(Request::route()->getAction()['as']);
        $this->prepareMenu();
        $this->prepareLocales();
        $this->obtainVersion();
        $this->user_roles = Auth::user()->roles;

    }

    private function getRouteParent(string $routeName)
    {
        $ex = explode('.', $routeName);

        return implode('.', [$ex[0] ?? '', $ex[1] ?? '']);
    }

    private function prepareMenu()
    {
        $menu = [];
        foreach (config('menu') as $key => $value) {
            $children = $value['children'] ?? false;
            $condition = $value['show_condition'] ?? false;
            if (!$condition || $this->{$condition}($value)) {
                $menu[$key] = $value;
                $menu[$key]['active'] = $children ?
                    $this->checkWhetherSomeChildrenActive($value['children']) : $this->isRouteActive($value['route']);

                if ($children) {
                    foreach ($children as $childKey => $childValue) {
                        $condition = $childValue['show_condition'] ?? false;

                        if (!$condition || $this->{$condition}($childValue)) {
                            $menu[$key]['children'][$childKey] = $childValue;
                            $menu[$key]['children'][$childKey]['active'] = $this->isRouteActive($childValue['route']);
                        } else {
                            unset($menu[$key]['children'][$childKey]);
                        }

                        if ($childValue['route'] == 'translations.preview') {
                            unset($menu[$key]['children'][$childKey]);
                        }
                    }
                }
            }
        }

        $this->menu = $menu;
    }

    private function checkWhetherSomeChildrenActive(array $children)
    {
        foreach ($children as $child) {
            if ($this->isRouteActive($child['route'])) {
                return true;
            }
        }

        return false;
    }

    private function isRouteActive($value)
    {
        return $this->getRouteParent($value) == $this->routeParent;
    }

    private function prepareLocales()
    {
        $this->locales = UtilService::getLocales();
    }

    private function obtainVersion()
    {
        $json = file_get_contents(base_path('package.json'));
        $this->version = json_decode($json)->version;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $restaurant = Restaurant::getCurrentRestaurant();
        if(isset($restaurant)){
            $settingsId=Settings::first()->id;
        }
        else
        {
            $settingsId=DB::table('settings')->first()->id??1;
        }

        return view('components.admin.layout.sidebar',['userRoles'=>$this->user_roles,'settingsId'=>$settingsId]);
    }

    private function isWebsite($value)
    {
        return $this->isWebsite;
    }

    private function isNotWebsite($value)
    {
        return !$this->isWebsite;
    }
}
