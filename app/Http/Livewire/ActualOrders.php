<?php

namespace App\Http\Livewire;

use App\Models\Bill;
use Illuminate\Support\Carbon;
use Livewire\Component;

class ActualOrders extends Component
{
    private $orders;

    public $page = 1;

    public $perPage = 1;

    public $numberOfOrders;

    public $status;

    public $time;

    public $class;

    public $hasBeenScrolled;

    private $isRefreshable = true;

    protected $listeners = ['actualOrdersSwipedLeft' => 'next', 'actualOrdersSwipedRight' => 'previous', 'refreshComponent' => 'refresh', 'actualOrdersScrolled' => 'elementHasBeenScrolled', 'showScrollElement' => 'showScrollElement', 'getActualNumberOfOrders' => 'getNumbersOfOrders', 'cancelStatus' => 'cancelStatus'];

    public function mount()
    {
        $this->numberOfOrders = $this->getOrdersBaseQuery()->count();
        $this->assignValues();
    }

    public function render()
    {
        return view(
            'livewire.actual-orders',
            [
                'orders' => $this->orders,
                'time' => $this->time,
            ]
        );
    }

    public function getNumbersOfOrders()
    {
        $this->dispatchBrowserEvent('getActualNumberOfOrders', ['numberOfOrders' => $this->numberOfOrders]);
        $this->refresh();
    }

    public function refresh()
    {
        $this->page = $this->page > $this->getOrdersBaseQuery()->count() ? $this->getOrdersBaseQuery()->count() : ($this->page == 0 ? 1 : $this->page);
        $this->assignValues();
    }

    public function next()
    {
        $this->page + 1 > $this->numberOfOrders ? $this->page = $this->numberOfOrders : $this->page++;
        $this->showScrollElement();
        $this->assignValues();
    }

    public function previous()
    {
        $this->page - 1 == 0 ? $this->page = 1 : $this->page--;
        $this->showScrollElement();
        $this->assignValues();
    }

    public function first()
    {
        $this->page = 1;
        $this->showScrollElement();
        $this->assignValues();
    }

    public function last()
    {
        $this->page = $this->numberOfOrders;
        $this->showScrollElement();
        $this->assignValues();
    }

    public function assignValues()
    {
        $this->orders = $this->getOrders();
        if ($this->orders) {
            $this->time = Carbon::parse($this->orders[0]->created_at);
            $this->status = $this->orders[0]->status;
            $this->numberOfOrders = $this->getOrdersBaseQuery()->count();

            $this->emitTo('bill-status', 'refreshComponent', $this->orders[0]);
        }
        $this->dispatchBrowserEvent('rerenderScrollBar');
        $this->dispatchBrowserEvent('getActualNumberOfOrders', ['numberOfOrders' => $this->numberOfOrders]);
        $this->dispatchBrowserEvent('refreshVueComponents');
        $this->dispatchEventForScrollable();
    }

    public function elementHasBeenScrolled()
    {
        $this->hasBeenScrolled = true;
        $this->refresh();
    }

    public function showScrollElement()
    {
        $this->hasBeenScrolled = false;
        $this->refresh();
    }

    public function dispatchEventForScrollable()
    {
        if (! $this->hasBeenScrolled) {
            $this->dispatchBrowserEvent('checkIfActualOrdersContentIsScrollable');
        }
    }

    public function cancelStatus()
    {
        $billId = $this->getOrders()[0]->id;
        $bill = Bill::where('id', $billId)->first();
        $bill->status = 4;
        $bill->save();
        $this->refresh();
    }

    private function getOrdersBaseQuery()
    {
        return Bill::query()->orderBy('created_at', 'ASC')->placed()->whereIn('status', [Bill::STATUS_ACCEPTED, Bill::STATUS_READY]);
    }

    private function getOrders()
    {
        return  $this->getOrdersBaseQuery()->paginate($this->perPage, ['*'], 'page', $this->page)->items();
    }
}
