<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\BoolStatusDecorator;
use App\Decorators\MoneyDecorator;
use App\Models\Refund;
use Rappasoft\LaravelLivewireTables\Views\Column;

class RefundsDatatable extends BaseDatatable
{
    protected $model = Refund::class;

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
            Column::make(__('refunds.Bill'), 'bill.id')
                ->sortable()
                ->searchable()
                ->collapseOnMobile(),
            Column::make(__('refunds.Payment'), 'payment.id')
                ->sortable()
                ->searchable(),
            Column::make(__('refunds.Amount'), 'amount')
                ->sortable()
                ->format(
                    fn ($value, Refund $row, Column $column) => (new MoneyDecorator())->decorate($value, 'PLN')
                ),
            Column::make(__('refunds.Status'), 'status')
                ->format(
                    fn ($value, Refund $row, Column $column) => __('refunds.'.$row->getStatusName())
                ),
            Column::make(__('refunds.Refunded'), 'refunded')
                ->sortable()
                ->format(
                    fn ($value, Refund $row, Column $column) => (new BoolStatusDecorator())->decorate($value)
                ),
            Column::make(__('admin.Actions'), 'id')
                ->format(
                    function ($value, Refund $row, Column $column) {
                        return view('components.admin.datatable.actions', [
                            'buttons' => [
                                $this->prepareShowButton($row->id, 'refund', 'refunds'),
                            ],
                        ]);
                    }
                )
                ->html(),

        ];
    }
}
