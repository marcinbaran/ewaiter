<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\MoneyDecorator;
use App\Models\TrWithdraw;
use Carbon\Carbon;
use Rappasoft\LaravelLivewireTables\Views\Column;

class TrWithdrawsDatatable extends BaseDatatable
{
    protected $model = TrWithdraw::class;

    public function configure(): void
    {
        $this->createLink = false;
        parent::configure();
    }

    public function columns(): array
    {
        return [
            Column::make(__('admin.ID'), 'id')
                ->sortable()
                ->searchable(),
            Column::make(__('admin.Restaurant'), 'restaurant.name')
                ->searchable()
                ->sortable(),
            Column::make(__('admin.Amount'), 'amount')
                ->searchable()
                ->sortable()
                ->format(fn ($value, TrWithdraw $row, Column $column) => MoneyDecorator::decorate($value, 'PLN')),
            Column::make(__('admin.Account number'), 'account_number')
                ->searchable(),
            Column::make(__('admin.Created at'), 'created_at')
                ->sortable()
                ->format(fn ($value, TrWithdraw $row, Column $column) => Carbon::parse($value)->format('Y-m-d H:i')),
        ];
    }
}
