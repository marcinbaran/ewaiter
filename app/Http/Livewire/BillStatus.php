<?php

namespace App\Http\Livewire;

use App\Http\Resources\Admin\BillResource;
use App\Models\Bill;
use App\Models\User;
use App\Services\FirebaseServiceV2;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BillStatus extends Component
{
    public $resource;

    public $status;

    public $billId;

    public $notActivatedStatusPointClass = 'bg-white w-6 h-6 relative z-20 rounded-full transform transition-all duration-75';

    public $activatedStatusPointClass = 'bg-blue-700 w-6 h-6 relative z-20 rounded-full transform transition-all duration-75 ';

    public $currentActiveStatusPointClass = 'bg-blue-700 w-10 h-10 relative z-20 rounded-full transform transition-all duration-75 ';

    public $statusBarActive = 'h-full w-full bg-blue-700';

    public $statusBarInactive = 'h-full w-0 bg-white';

    public $statusBarNext = 'h-full w-0 bg-white status-bar';

    public $statusBarCancel = 'h-full w-full bg-red-700';

    public $cancelStatusPointClass = 'bg-red-700 w-6 h-6 relative z-20 rounded-full transform transition-all duration-75 ';

    public $statusBarComplaint = 'h-full w-full bg-yellow-400';

    public $complaintStatusPointClass = 'bg-yellow-400 w-6 h-6 relative z-20 rounded-full transform transition-all duration-75 ';

    public $isUserAdmin = false;

    public $statusElements = [
        [
            'pointClass' => '',
        ],
        [
            'pointClass' => '',
            'statusBar' => '',
        ],
        [
            'pointClass' => '',
            'statusBar' => '',
        ],
        [
            'pointClass' => '',
            'statusBar' => '',
        ],
    ];

    protected $listeners = ['refreshComponent' => 'setData', 'getCurrentBillStatus' => 'getBillStatus'];

    private $data;

    public function getBillStatus()
    {
        $this->dispatchBrowserEvent('billStatus', ['status' => $this->status]);
    }

    public function mount()
    {
        $this->isUserAdmin = (bool) count(array_filter(Auth::user()->roles, fn ($role) => $role === User::ROLE_ADMIN));
        $billId = $this->billId ?? request()->route()->parameter('bill');
        $this->setData($billId);
    }

    public function setData(Bill $billId)
    {
        $this->data = new BillResource($billId);
        $this->resource = $this->data->resource;
        $this->status = $this->data->resource->status;
        $this->billId = $this->data->id;
        $this->setStatus();
    }

    public function setStatus()
    {
        $this->dispatchBrowserEvent('billStatus', ['status' => $this->status]);
        foreach ($this->statusElements as $key => $statusElement) {
            $this->statusElements[$key]['pointClass'] = $this->notActivatedStatusPointClass;
            $this->statusElements[$key]['statusBar'] = $this->statusBarInactive;
        }

        for ($i = 0; $i < $this->status + 1; $i++) {
            $this->statusElements[$i]['pointClass'] = $this->activatedStatusPointClass;
            $this->statusElements[$i]['statusBar'] = $this->statusBarActive;
        }

        if ($this->status < 3) {
            $this->statusElements[$this->status]['pointClass'] = $this->currentActiveStatusPointClass;
            $this->statusElements[$this->status + 1]['statusBar'] = $this->statusBarNext;
        }

        if ($this->status == 3) {
            $this->statusElements[$this->status]['pointClass'] = $this->currentActiveStatusPointClass;
        }

        if ($this->status == 4) {
            foreach ($this->statusElements as $key => $statusElement) {
                $this->statusElements[$key]['pointClass'] = $this->cancelStatusPointClass;
                $this->statusElements[$key]['statusBar'] = $this->statusBarCancel;
            }
        }

        if ($this->status == 5) {
            foreach ($this->statusElements as $key => $statusElement) {
                $this->statusElements[$key]['pointClass'] = $this->complaintStatusPointClass;
                $this->statusElements[$key]['statusBar'] = $this->statusBarComplaint;
            }
        }
    }

    public function render()
    {
        return view('livewire.bill-status');
    }

    public function next()
    {
        if ($this->resource->time_wait == null) {
            $this->dispatchBrowserEvent('toastActualOrdersTimeWaitError', ['message' => __('orders.Set the time of order')]);

            return;
        }
        $this->status = $this->status + 1 > 3 ? 3 : $this->status + 1;

        if ($this->status == 3) {
            $this->dispatchBrowserEvent('toastActualOrders', ['message' => __('orders.Order released')]);
        }
        $this->resource->status = $this->status;
        $this->emitTo('actual-orders', 'refreshComponent');
        if ($this->status == 3)
        {
            $this->resource->released_at = Carbon::now();
        }
        if ($this->status == 1)
        {
            $deliveryTime = Carbon::parse($this->resource->time_wait)->format('Y-m-d H:i');

            if ($this->resource->delivery_type == 'delivery_table') {
                FirebaseServiceV2::saveNotification(
                    $this->resource->user_id,
                    __('firebase.Your order has been accepted [with time, table delivery]', ['delivery_time' => $deliveryTime]),
                    '/account/orders_history/' . $this->resource->id,
                    $this->resource->id
                );
            } elseif ($this->resource->delivery_type == 'delivery_personal_pickup') {
                FirebaseServiceV2::saveNotification(
                    $this->resource->user_id,
                    __('firebase.Your order has been accepted [with time, personal pickup]', ['delivery_time' => $deliveryTime]),
                    '/account/orders_history/' . $this->resource->id,
                    $this->resource->id
                );
            } else {
                FirebaseServiceV2::saveNotification(
                    $this->resource->user_id,
                    __('firebase.Your order has been accepted [with time]', ['delivery_time' => $deliveryTime]),
                    '/account/orders_history/' . $this->resource->id,
                    $this->resource->id
                );
            }
        }

        $this->resource->save();
        $this->setStatus();
    }

    public function cancel()
    {
        $this->status = $this->status < 3 ? 4 : 5;
        $this->dispatchBrowserEvent('toastActualOrders', ['message' => __('orders.Order cancelled')]);
        $this->resource->status = $this->status;
        $this->emitTo('actual-orders', 'refreshComponent');
        $this->resource->time_wait = null;
        $this->resource->save();
        $this->setStatus();
    }
}
