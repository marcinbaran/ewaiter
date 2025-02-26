<?php

namespace App\Http\Livewire;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Component;

class IconPicker extends Component
{
    public string $icon = '';

    public string $iconId;

    public string $search = '';

    public $initialIcon;

    public array $icons = [];

    public array $displayIcons = [];

    public function __construct()
    {
        $this->icons = config('tagicon');
        foreach ($this->icons as $key => $icon) {
            $this->icons[$key]['isActive'] = false;
        }
        $this->displayIcons = $this->icons;
        $this->icon = Arr::first($this->icons)['icon'] ?? '';
    }

    public function mount()
    {
        if ($this->initialIcon) {
            $this->icon = $this->icons[$this->initialIcon]['icon'] ?? $this->icon;
            $this->iconId = $this->initialIcon;
        }
    }

    public function selectIcon($id)
    {
        $this->icon = $this->icons[$id]['icon'] ?? $this->icon;
        $this->iconId = $id ?? $this->iconId;
        $this->highlightIcon($id);
    }

    public function highlightIcon($id)
    {
        if (in_array($id, array_keys($this->displayIcons))) {
            $this->displayIcons[$id]['isActive'] = true;
        }
        foreach ($this->displayIcons as $key => $icon) {
            if ($key != $id) {
                $this->displayIcons[$key]['isActive'] = false;
            }
        }
    }

    public function updatedSearch()
    {
        $this->searchIcon();
    }

    public function searchIcon()
    {
        $this->displayIcons = $this->search != '' ? array_filter($this->icons, function ($icon) {
            return Str::contains(strtolower($icon['name']), strtolower($this->search));
        }) : $this->icons;

        $this->highlightIcon($this->iconId ?? null);
    }

    public function render()
    {
        return view('livewire.icon-picker');
    }
}
