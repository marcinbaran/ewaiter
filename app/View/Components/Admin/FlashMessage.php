<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FlashMessage extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.flash-message');
    }

    public function getTextColor(string $type)
    {
        $color = '';
        switch ($type) {
            case 'success':
                $color = 'text-green-600 dark:text-green-400';
                break;
            case 'danger':
                $color = 'text-red-600 dark:text-red-500';
                break;
            case 'warning':
                $color = 'text-yellow-500 dark:text-yellow-400';
                break;
            default:
            case 'info':
                $color = 'text-blue-600 dark:text-blue-500';
                break;
        }

        return $color;
    }

    public function getBackgroundColor(string $type)
    {
        $color = '';
        switch ($type) {
            case 'success':
                $color = 'bg-green-600 dark:bg-green-400';
                break;
            case 'danger':
                $color = 'bg-red-600 dark:bg-red-500';
                break;
            case 'warning':
                $color = 'bg-yellow-500 dark:bg-yellow-400';
                break;
            default:
            case 'info':
                $color = 'bg-blue-600 dark:bg-blue-500';
                break;
        }

        return $color;
    }
}
