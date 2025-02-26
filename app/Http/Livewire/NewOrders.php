<?php

namespace App\Http\Livewire;

use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class NewOrders extends Component
{
    use AuthorizesRequests;

    private $orders;

    public $page = 1;

    public $perPage = 1;

    public $billId;

    public $waitTime;

    public $error = false;

    public $calculatedDeliveryTime;

    public $status;

    public $numberOfOrders;

    public $time;

    public $class;

    public $seenOrders = [];

    public $seenOrder = false;

    public $hasUnseen = false;

    public $numberOfUnseenOrders = 0;

    public $ChangeAmount = 5;

    public $displayClass = 'hidden';

    public $hasBeenScrolled = false;

    protected $listeners = ['newOrdersSwipedLeft' => 'next', 'newOrdersSwipedRight' => 'previous', 'setTimeWait' => 'waitTimeInputUpdate', 'updateWaitTimeValue' => 'waitTimeInputUpdated', 'newOrdersScrolled' => 'elementHasBeenScrolled', 'showScrollElement' => 'showScrollElement', 'getNewNumberOfOrders' => 'getNumberOfOrders'];

    public function mount()
    {
        $this->isRefreshable = true;
        $this->seenOrders = app('session')->get('seenOrders', []);
        $this->assignValues();
    }

    public function render()
    {
        return view('livewire.new-orders', [
            'orders' => $this->orders,
            'time' => $this->time,
        ]);
    }

    public function getNumberOfOrders()
    {
        $this->dispatchBrowserEvent('getNewNumberOfOrders', ['numberOfOrders' => $this->numberOfOrders]);
        $this->refresh();
    }

    public function UpdatedWaitTime($waitTime)
    {
        if ($waitTime >= 0) {
            $this->waitTime = $waitTime;
        }
        $this->error = false;
        $this->calculatedDeliveryTime = $this->calculateDeliveryTime();
        $this->refresh();
    }

    public function increaseValue()
    {
        $this->checkIfWaitTimeIsEmpty();
        $this->waitTime += $this->ChangeAmount;
        $this->error = false;
        $this->calculatedDeliveryTime = $this->calculateDeliveryTime();
        $this->refresh();
    }

    public function decreaseValue()
    {
        $this->checkIfWaitTimeIsEmpty();
        $this->waitTime -= $this->ChangeAmount;
        if ($this->waitTime < 0) {
            $this->waitTime = 0;
        }
        $this->error = false;
        $this->calculatedDeliveryTime = $this->calculateDeliveryTime();
        $this->refresh();
    }

    public function checkIfWaitTimeIsEmpty()
    {
        if ($this->waitTime == null) {
            $this->waitTime = 0;
        }
    }

    public function refresh()
    {
        $this->numberOfOrders > 0 ? $this->emit('newOrdersUnseen') : '';
        $this->assignValues();
    }

    public function next()
    {
        $this->page + 1 > $this->numberOfOrders ? $this->page = $this->numberOfOrders : $this->page++;
        $this->resetValues();
        $this->assignValues();
    }

    public function previous()
    {
        $this->page - 1 == 0 ? $this->page = 1 : $this->page--;
        $this->resetValues();
        $this->assignValues();
    }

    public function first()
    {
        $this->page = 1;
        $this->resetValues();
        $this->assignValues();
    }

    public function last()
    {
        $this->page = $this->numberOfOrders;
        $this->resetValues();
        $this->assignValues();
    }

    public function assignValues()
    {
        $this->orders = $this->getOrders();
        if ($this->orders) {
            $this->time = Carbon::parse($this->orders[0]->created_at);
            $this->status = $this->orders[0]->status;
            $orderId = $this->orders[0]->id;
            $newNumberOfOrders = $this->getOrdersBaseQuery()->count();
            if ($newNumberOfOrders > $this->numberOfOrders) {
                $this->emit('newOrderArrived');
            }
            $this->numberOfOrders = $newNumberOfOrders;
            $this->checkForUnseenOrders($orderId);
            $this->displayClass = 'hidden';
        } else {
            $this->displayClass = 'block';
        }
        $this->dispatchBrowserEvent('rerenderScrollBar');
        $this->dispatchBrowserEvent('getNewNumberOfOrders', ['numberOfOrders' => $this->numberOfOrders]);
        $this->dispatchBrowserEvent('refreshVueComponents');
        $this->dispatchEventForScrollable();
    }

    public function elementHasBeenScrolled()
    {
        $this->hasBeenScrolled = true;
        $this->refresh();
    }

    public function dispatchEventForScrollable()
    {
        if (! $this->hasBeenScrolled) {
            $this->dispatchBrowserEvent('checkIfNewOrdersContentIsScrollable');
        }
    }

    public function showScrollElement()
    {
        $this->hasBeenScrolled = false;
        $this->refresh();
    }

    public function checkForUnseenOrders($orderId)
    {
        $this->seenOrder = in_array($orderId, $this->seenOrders);
        $this->hasUnseen = $this->numberOfOrders > count($this->seenOrders);
        if (! $this->seenOrder) {
            $this->seenOrders[] = $orderId;
            app('session')->put('seenOrders', $this->seenOrders);
        }
        if ($this->hasUnseen) {
            $this->numberOfUnseenOrders = $this->numberOfOrders - count($this->seenOrders);
        }
    }

    public function removeOrderFromSession($orderId)
    {
        $this->seenOrders = array_diff($this->seenOrders, [$orderId]);
        app('session')->put('seenOrders', $this->seenOrders);
        $this->refresh();
    }

    public function resetValues()
    {
        $this->waitTime = null;
        $this->error = false;
        $this->calculatedDeliveryTime = null;
        $this->showScrollElement();
        $this->dispatchBrowserEvent('resetInputValue');
    }

    public function acceptStatus()
    {
        $billId = $this->getOrders()[0]->id;
        $bill = Bill::where('id', $billId)->first();
        $bill->status = 1;

        if ($this->waitTime > 0) {
            $this->page = $this->page == 1 ? 1 : $this->page - 1;
            $bill->time_wait = $this->calculateDeliveryTime()->format('Y-m-d H:i:s');
            $bill->save();
            $this->dispatchBrowserEvent('resetInputValue');
            $this->waitTime = null;
            $this->calculatedDeliveryTime = null;
            $this->removeOrderFromSession($billId);
            $this->emit('newOrderAccepted');

            $this->refresh();
        } else {
            $this->error = true;
            $this->refresh();
        }
    }

    public function calculateDeliveryTime()
    {
        $currentDate = Carbon::now();
        $deliveryTime = $currentDate->addMinutes($this->waitTime);

        return $deliveryTime;
    }

    public function cancelStatus()
    {
        $billId = $this->getOrders()[0]->id;
        $bill = Bill::where('id', $billId)->first();
        $bill->status = 4;
        $this->page = $this->page == 1 ? 1 : $this->page - 1;
        $this->removeOrderFromSession($billId);
        $this->emit('newOrderCancelled');
        $bill->save();

        $this->refresh();
    }

    private function getOrdersBaseQuery()
    {
        return Bill::query()->placed()->orderBy('created_at', 'ASC')->where('status', '==', '0');
    }

    private function getOrders()
    {
        return  $this->getOrdersBaseQuery()->paginate($this->perPage, ['*'], 'page', $this->page)->items();
    }
}
