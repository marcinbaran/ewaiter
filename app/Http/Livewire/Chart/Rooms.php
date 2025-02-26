<?php

namespace App\Http\Livewire\Chart;

use App\Models\Bill;
use Livewire\Component;

class Rooms extends Component
{
    public $data = [];

    public $dayFilter = 'all';

    public $html_id = 'rooms-chart-container';

    public function mount()
    {
        $this->getResults();
    }

    public function getResults()
    {
        $query = Bill::query()
            ->whereNotNull('room_delivery');

        switch($this->dayFilter) {
            case 'day':
                $query->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]);
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfDay()->subWeek(), now()->endOfDay()]);
                break;
            case 'month':
                $query->whereBetween('created_at', [now()->startOfDay()->subMonth(), now()->endOfDay()]);
                break;
            case 'year':
                $query->whereBetween('created_at', [now()->startOfDay()->subYear(), now()->endOfDay()]);
                break;
            case 'all':
            default:
                $query->whereBetween('created_at', [now()->startOfDay()->subYears(20), now()->endOfDay()]);
                break;
        }

        $data = [];
        /** @var Bill $bill */
        foreach ($query->get() as $bill) {
            if (! isset($data[$bill->room->name])) {
                $data[$bill->room->name] = 0;
            }
            $data[$bill->room->name] += $bill->getFullPrice();
        }

        $this->data = [
            'labels' => array_keys($data),
            'datasets' => [
                [
                    'label' => 'Przychody',
                    'data' => array_values($data),
                ],
            ],
        ];
    }

    public function render()
    {
        return view('livewire.chart.rooms');
    }

    public function updatedSeatFilter()
    {
        $this->getResults();
        $this->dispatchBrowserEvent('chart-data-updated', ['container_class' => $this->html_id]);
    }

    public function updatedDayFilter()
    {
        $this->getResults();
        $this->dispatchBrowserEvent('chart-data-updated', ['container_class' => $this->html_id]);
    }
}
