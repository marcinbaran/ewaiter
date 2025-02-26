<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\BoolStatusDecorator;
use App\Decorators\MoneyDecorator;
use App\Models\Payment;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PaymentsDatatable extends BaseDatatable
{
    protected $model = Payment::class;

    public function configure(): void
    {
        $this->createLink = false;
        parent::configure();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()
                ->collapseOnMobile(),
            Column::make(__('orders.Billid'), 'bill_id')
                ->sortable()
                ->searchable()
                ->collapseOnMobile(),
            Column::make(__('payments.p24_amount'), 'p24_amount')
                ->sortable()
                ->html()
                ->format(
                    fn ($value, Payment $row, Column $column) => (new MoneyDecorator())->decorate($value / 100, 'PLN')
                ),
            Column::make(__('payments.p24_currency'), 'p24_currency')
                ->sortable(),
            Column::make(__('payments.paid'), 'paid')
                ->sortable()
                ->html()
                ->format(
                    fn ($value, Payment $row, Column $column) => (new BoolStatusDecorator())->decorate($value)
                ),
            Column::make(__('admin.Actions'), 'id')
                ->format(
                    function ($value, Payment $row, Column $column) {
                        return view('components.admin.datatable.actions', [
                            'buttons' => [
                                $this->prepareShowButton($row->id, 'payment', 'payments'),
                            ],
                        ]);
                    }
                )
                ->html(),

        ];
    }
}
