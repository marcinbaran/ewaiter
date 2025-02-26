<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\BoolStatusDecorator;
use App\Decorators\Editable\Bill\PaidEditableDecorator;
use App\Decorators\Editable\Bill\StatusEditableDecorator;
use App\Decorators\Editable\Bill\TimeWaitEditableDecorator;
use App\Decorators\MoneyDecorator;
use App\Decorators\OrderStatusDecorator;
use App\Http\Filters\Bill\CreatedAtFrom;
use App\Http\Filters\Bill\CreatedAtTo;
use App\Http\Filters\Bill\DeliveryType;
use App\Http\Filters\Bill\IsPaid;
use App\Http\Filters\Bill\PaymentType;
use App\Http\Filters\Bill\Status;
use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class BillsDatatable extends BaseDatatable
{
    protected $model = Bill::class;

    public function configure(): void
    {
        $this->createLink = false;
        $this->boldColumn = 'created_at';
        $this->setFilterLayoutSlideDown();
        $this->setSingleSortingDisabled();
        $this->setDefaultSort('created_at', 'desc');
        $this->setAdditionalSelects(['*']);

        parent::configure();
    }

    public function columns(): array
    {
        return [
            Column::make(__('admin.ID'), 'id')
                ->sortable(),
            Column::make(__('admin.Created at'), 'created_at')
                ->sortable()
                ->format(
                    fn ($value, Bill $row, Column $column) => Carbon::parse($row->created_at)->format('Y-m-d')
                ),
            Column::make(__('orders.Type of delivery'), 'delivery_type')
                ->sortable()
                ->searchable()
                ->format(fn ($value, Bill $row, Column $column) => $row->getTypeDelivery()),
            Column::make(__('orders.Delivery time'), 'time_wait')
                ->html()
                ->format(
                    fn ($value, Bill $row, Column $column) => in_array($row->status, [Bill::STATUS_CANCELED, Bill::STATUS_COMPLAINT, Bill::STATUS_RELEASED]) ? Carbon::parse($row->time_wait)->format('H:i') :
                        TimeWaitEditableDecorator::create($row, $value)->decorate()
                ),
            Column::make(__('orders.Payment'), 'paid')
                ->sortable()
                ->format(fn ($value, Bill $row, Column $column) => in_array($row->status, [Bill::STATUS_CANCELED, Bill::STATUS_COMPLAINT, Bill::STATUS_RELEASED]) ? '<div style="display: flex; gap: 0.25rem; align-items: center">'.(new BoolStatusDecorator)->decorate($value).' '.$row->getTypePayment().'</div>' : '<div style="display: flex; gap: 0.25rem; align-items: center">'.PaidEditableDecorator::create($row, $value)->decorate().' '.$row->getTypePayment().'</div>')
                ->html(),
            Column::make(__('admin.Price'), 'price')
                ->sortable()
                ->html()
                ->format(
                    fn ($value, Bill $row, Column $column) => (new MoneyDecorator())->decorate($row->getFullPrice(), 'PLN')
                ),
            Column::make(__('admin.Points'), 'points_value')
                ->sortable()
                ->format(fn ($value, Bill $row, Column $column) => $value !== '0.00' ? (new MoneyDecorator())->decorate($value, 'PLN') : '-')
                ->html(),
            Column::make(__('orders.Status'), 'status')
                ->sortable()
                ->format(fn ($value, Bill $row, Column $column) => in_array($row->status, [Bill::STATUS_CANCELED, Bill::STATUS_COMPLAINT, Bill::STATUS_NEW]) ? (new OrderStatusDecorator())->decorate($row) : StatusEditableDecorator::create($row, $value)->decorate()),
            Column::make(__('admin.Actions'), 'id')
                ->format(
                    function ($value, Bill $row, Column $column) {
                        return view('components.admin.datatable.actions', [
                            'buttons' => [
                                $this->prepareShowButton($row->id, 'bill', 'bills'),
                            ],
                        ]);
                    }
                )
                ->html(),

        ];
    }

    public function filters(): array
    {
        return [
            (new Status())->prepare(),
            (new IsPaid())->prepare(),
            (new PaymentType())->prepare(),
            (new DeliveryType())->prepare(),
            (new CreatedAtFrom())->prepare(),
            (new CreatedAtTo())->prepare(),
        ];
    }

    public function builder(): Builder
    {
        return $this->getModel()::placed()->with($this->getRelationships());
    }
}
