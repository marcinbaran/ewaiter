<?php

namespace App\Http\Livewire\Chart;

use App\Models\Bill;
use App\Models\Table;
use Livewire\Component;

class Tables extends Component
{
    public $data = [];

    public $uniqueSeats = [];

    public $seatFilter = 'all';

    public $dayFilter = 'all';

    public $html_id = 'tables-chart-container';

    public function mount()
    {
        Table::query()->select('people_number')->get()->each(function ($table) {
            $this->uniqueSeats[$table->people_number] = $table->people_number;
        });

        $this->getResults();
    }

    public function getResults()
    {
        $query = Bill::query()
            ->whereNotNull('table_number');

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

        if ($this->seatFilter && $this->seatFilter != 'all') {
            $query->whereHas('table', function ($query) {
                $query->where('people_number', $this->seatFilter);
            });
        }

        $data = [];
        /** @var Bill $bill */
        foreach ($query->get() as $bill) {
            if (! $bill->table instanceof Table) {
                continue;
            }
            if (! isset($data[$bill->table->name])) {
                $data[$bill->table->name] = 0;
            }
            $data[$bill->table->name] += $bill->getFullPrice();
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
        return view('livewire.chart.tables');
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
