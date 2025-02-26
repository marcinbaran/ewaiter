<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\BoolStatusDecorator;
use App\Decorators\MoneyDecorator;
use App\Models\AdditionGroup;
use App\Models\Transaction;
use Carbon\Carbon;
use Rappasoft\LaravelLivewireTables\Views\Column;

class TransactionsDatatable extends BaseDatatable
{
    protected $model = Transaction::class;

    public function configure(): void
    {
        $this->createLink = false;
        parent::configure();
    }

    public function columns(): array
    {
        return [
            Column::make(__('admin.ID'), 'id')
                ->sortable(),
            Column::make(__('admin.Restaurant'), 'restaurant.name')
                ->sortable()
                ->searchable(),
            Column::make(__('admin.Amount'), 'amount')
                ->sortable()
                ->html()
                ->format(
                    fn ($value, AdditionGroup $row, Column $column) => (new MoneyDecorator())->decorate($value, 'PLN')
                ),
            Column::make(__('admin.Withdrawal'), 'withdrawal')
                ->sortable()
                ->html()
                ->format(
                    fn ($value, AdditionGroup $row, Column $column) => (new BoolStatusDecorator())->decorate($value)
                ),
            Column::make(__('admin.Withdrawal at'), 'withdrawal_at')
                ->sortable()
                ->format(
                    fn ($value, AdditionGroup $row, Column $column) => Carbon::parse($value)->format('Y-m-d')
                ),
            Column::make(__('admin.Created at'))
                ->sortable()
                ->format(
                    fn ($value, AdditionGroup $row, Column $column) => Carbon::parse($row->created_at)->format('Y-m-d')
                ),

        ];
    }
}
