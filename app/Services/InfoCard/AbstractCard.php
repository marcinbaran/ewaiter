<?php

namespace App\Services\InfoCard;

abstract class AbstractCard
{
    /** @var string */
    protected $title = '';

    /** @var string */
    protected $value = '';

    /** @var string */
    protected $color = '';

    /** @var string */
    protected $icon = '';

    abstract public function setValue();

    public function toArray()
    {
        $this->setValue();

        return [
            'title' => $this->title,
            'value' => $this->value,
            'color' => $this->color,
            'icon'  => $this->icon,
        ];
    }
}
