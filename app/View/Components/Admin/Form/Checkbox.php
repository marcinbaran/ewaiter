<?php

namespace App\View\Components\Admin\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Checkbox extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $checked = false,
        public $value = 1,
        public $disabled = false
    ) {
        //
    }

    public function isChecked()
    {
        return $this->value == $this->checked;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.form.checkbox');
    }
}
