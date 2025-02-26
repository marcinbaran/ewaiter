<?php

namespace App\Providers;

use App\Http\Helpers\GroupTranslator;
use Illuminate\Support\ServiceProvider;

class HelperProvider extends ServiceProvider
{
    /**
     * The helper mappings for the application.
     *
     * @var array
     */
    protected $helpers = [
        'gtrans' => GroupTranslator::class.'::group_trans',
    ];

    /**
     * Bootstrap the application helpers.
     */
    public function boot()
    {
        foreach ($this->helpers as $alias => $method) {
            if (! function_exists($alias)) {
                eval("function {$alias}(...\$args) { return {$method}(...\$args); }");
            }
        }
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
    }
}
