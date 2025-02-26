<?php

namespace App\Decorators;

use Illuminate\Database\Eloquent\Model;

class EditableColumnDecorator
{
    private Model $model;

    private string $column;

    private string $type;

    private mixed $currentValue;

    private ?array $options = [];

    private string $label;

    private array $validationRules = [];

    public function decorate()
    {
        if ($this->type == 'select') {
            $array = [];
            foreach ($this->options as $option) {
                $array[] = [
                    'value' => $option['value'],
                    'label' => $option['label'],
                    'selected' => $option['value'] == $this->model->{$this->column},
                ];
            }
            $this->options = $array;
        }

        return view('admin.partials.decorators.editable-column', [
            'id' => $this->model->id,
            'url' => route('admin.common.editable'),
            'class' => get_class($this->model),
            'column' => $this->column,
            'type' => $this->type,
            'currentValue' => $this->currentValue,
            'options' => $this->options,
            'label' => $this->label ?? '',
            'validationRules' => json_encode($this->validationRules),
        ]);
    }

    /**
     * @param Model $model
     * @return $this
     */
    public function setModel(Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function setColumn(string $column): self
    {
        $this->column = $column;

        return $this;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param mixed $currentValue
     * @return $this
     */
    public function setCurrentValue(mixed $currentValue): self
    {
        $this->currentValue = $currentValue;

        return $this;
    }

    /**
     * @param array|null $options
     * @return $this
     */
    public function setOptions(?array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function setValidationRules(array $rules): self
    {
        $this->validationRules = $rules;

        return $this;
    }
}
