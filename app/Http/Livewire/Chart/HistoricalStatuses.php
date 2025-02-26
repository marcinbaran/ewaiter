<?php

namespace App\Http\Livewire\Chart;

use App\Models\Bill;
use Carbon\Carbon;
use Livewire\Component;

class HistoricalStatuses extends Component
{
    public $data = [];

    public $statuses = [];

    public $dayFilter = '';

    public $html_id = 'historical-statuses-chart-container';

    protected $listeners = [
        'dateChanged' => 'onChangeFilter',
    ];

    public function mount()
    {
        if (! $this->dayFilter) {
            $this->dayFilter = now()->subDay()->format('Y-m-d');
        }

        $this->getResults();
    }

    public function render()
    {
        return view('livewire.chart.historical-statuses');
    }

    public function getResults()
    {
        $query = Bill::query()
            ->whereBetween('created_at', [
                Carbon::parse($this->dayFilter)->startOfDay(),
                Carbon::parse($this->dayFilter)->endOfDay(),
            ]);

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

    public function onChangeFilter()
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
