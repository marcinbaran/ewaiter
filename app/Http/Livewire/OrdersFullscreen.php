<?php

namespace App\Http\Livewire;

use Livewire\Component;

class OrdersFullscreen extends Component
{
    public $count = 0;

    public $showFullscreen;

    protected $listeners = ['fullscreen' => 'toggleFullscreen'];

    public function render()
    {
        return view('livewire.orders-fullscreen');
    }

    public function increment()
    {
        $this->count++;
    }

    public function toggleFullscreen()
    {
        $this->showFullscreen = ! $this->showFullscreen;
    }
}
