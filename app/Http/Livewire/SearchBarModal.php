<?php

namespace App\Http\Livewire;

use App\Services\GlobalSearch\GlobalSearch;
use Livewire\Component;

class SearchBarModal extends Component
{
    private GlobalSearch $globalSearch;

    public array $data = [];

    public string $phrase = '';

    public function __construct()
    {
        $this->globalSearch = app(GlobalSearch::class);
    }

    public function mount()
    {
    }

    public function updatedPhrase($field)
    {
        if (strlen($field) > 2) {
            $this->runSearch($field);
        }
    }

    public function render()
    {
        return view('livewire.search-bar-modal');
    }

    private function runSearch(string $phrase = '')
    {
        $this->data = $this->globalSearch->search($phrase);
    }
}
