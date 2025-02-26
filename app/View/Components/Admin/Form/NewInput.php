<?php

namespace App\View\Components\Admin\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NewInput extends Component
{
    private $initialValue = [
        'name' => '',
        'id' => '',
        'type' => 'text',
        'value' => '',
        'oldValue' => '',
        'uncheckedValue' => '',
        'disabled' => false,
        'required' => false,
        'readonly' => false,
        'placeholder' => '',
        'min' => null,
        'max' => null,
        'step' => 1,
        'rows' => 4,
        'error' => '',
        'class' => '',
        'containerClass' => '',
        'checked' => false,
        'mode' => '',
        'prefix' => '',
        'suffix' => '',
        'showIcon' => true,
        'nullOption' => '',
    ];

    private $eligibleTypes = ['textarea', 'email', 'phone', 'postal-code', 'bank-account', 'money', 'percent', 'time', 'date', 'date-time', 'password', 'number', 'text', 'toggle', 'select', 'text-select', 'number-select', 'hidden'];

    public function __construct(
        public $name = null,
        public $id = null,
        public $type = null,
        public $value = null,
        public $oldValue = null,
        public $uncheckedValue = null,
        public $disabled = null,
        public $required = null,
        public $readonly = null,
        public $placeholder = null,
        public $min = null,
        public $max = null,
        public $minTime = null,
        public $step = null,
        public $rows = null,
        public $error = null,
        public $class = null,
        public $containerClass = null,
        public $checked = null,
        public $mode = null,
        public $prefix = null,
        public $suffix = null,
        public $showIcon = null,
        public $nullOption = null
    ) {
        $this->ensureCorrectType();
        $this->assignDefaultValues();
        $this->assignDefaultTypeValues();
        $this->ensureCorrectValues();
        $this->fixStringValueHtmlSpecialChars();
    }

    private function ensureCorrectType(): void
    {
        if (! in_array($this->type, $this->eligibleTypes)) {
            $this->type = $this->initialValue['type'];
        }
    }

    private function assignDefaultValues(): void
    {
        foreach ($this->initialValue as $key => $value) {
            if (is_null($this->$key)) {
                $this->$key = $value;
            }
        }
    }

    private function assignDefaultTypeValues(): void
    {
        if ($this->type == 'toggle') {
            $this->value = $this->value == '' ? 1 : $this->value;
            $this->uncheckedValue = $this->uncheckedValue == '' ? 0 : $this->uncheckedValue;
        } elseif ($this->type == 'select') {
            $this->mode = $this->mode == '' ? 'single' : $this->mode;
        } elseif ($this->type == 'percent') {
            $this->min = $this->min == null ? 0 : $this->min;
            $this->max = $this->max == null ? 100 : $this->max;
        }
    }

    private function ensureCorrectValues(): void
    {
        $this->disabled = $this->disabled == 'true';
        $this->required = $this->required == 'true';
        $this->readonly = $this->readonly == 'true';
        if (! $this->type == 'time') {
            $this->min = floatval($this->min);
            $this->max = floatval($this->max);
        }
        $this->step = floatval($this->step);
        $this->rows = intval($this->rows);
        if ($this->type == 'select') {
            $this->mode = $this->mode == 'multiple' ? 'multiple' : 'single';
        }
    }

    private function fixStringValueHtmlSpecialChars(): void
    {
        $this->value = htmlspecialchars_decode($this->value);
        $this->oldValue = htmlspecialchars_decode($this->oldValue);
        $this->uncheckedValue = htmlspecialchars_decode($this->uncheckedValue);
        $this->placeholder = htmlspecialchars_decode($this->placeholder);
        $this->error = htmlspecialchars_decode($this->error);
        $this->prefix = htmlspecialchars_decode($this->prefix);
        $this->suffix = htmlspecialchars_decode($this->suffix);
    }

    public function render(): View|Closure|string
    {
        return view('components.admin.form.new-input');
    }
}
