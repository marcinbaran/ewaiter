<?php

namespace App\Http\Livewire\Chart;

use App\Models\Bill;
use Livewire\Component;

class Statuses extends Component
{
    public $data = [];

    public $statuses = [];

    public function render()
    {
        return view('livewire.chart.statuses');
    }

    public function mount()
    {
        $this->getResults();
    }

    public function getResults()
    {
        $query = Bill::query()
            ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]);

        $data = [
            Bill::getStatusName(0) => 0,
            Bill::getStatusName(1) => 0,
            Bill::getStatusName(2) => 0,
            Bill::getStatusName(3) => 0,
            Bill::getStatusName(4) => 0,
            Bill::getStatusName(5) => 0,
        ];
        /** @var Bill $bill */
        foreach ($query->get() as $bill) {
            $data[Bill::getStatusName($bill->status)]++;
        }

        $labels = [];
        foreach (array_keys($data) as $key) {
            $labels[] = __('bills.'.ucfirst($key));
        }
        $this->data = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Przychody',
                    'data' => array_values($data),
                ],
            ],
        ];
    }
}
