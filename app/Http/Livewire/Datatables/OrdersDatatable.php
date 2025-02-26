<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\BoolStatusDecorator;
use App\Models\Order;
use App\Models\User;
use App\Services\Datatable\ActionButton;
use Carbon\Carbon;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class OrdersDatatable extends DataTableComponent
{
    protected $model = Order::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');

        $this->setAdditionalSelects(['users.last_name as last_name']);

        $this->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {
            if (! $column->isField('first_name')) {
                return [
                    'default' => false,
                    'class'   => 'px-6 py-4 whitespace-nowrap text-sm font-light dark:text-white',
                ];
            }

            return [];
        });
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable(),
            Column::make('Bill ID', 'bill_id')
                ->sortable()
                ->searchable(),
            Column::make('Table name', 'table.name')
                ->sortable()
                ->searchable(),
            Column::make('Price', 'price')
                ->sortable(),
            Column::make('Paid', 'paid')
                ->sortable()
                ->format(fn ($value, Order $user, Column $column) => (new BoolStatusDecorator())->decorate($value)),
            Column::make('Dish', 'dish.name')
                ->searchable(),
            Column::make('Status', 'status')
                ->sortable()
//                ->format(fn($value, User $user, Column $column) => (new BoolStatusDecorator())->decorate($value))
            ,
            Column::make(__('admin.Created at'), 'created_at')
                ->sortable()
                ->format(
                    fn ($value, Order $row, Column $column) => Carbon::parse($row->created_at)->format('Y-m-d')
                ),
            Column::make(__('admin.Actions'), 'id')
                ->format(
                    function ($value, Order $row, Column $column) {
                        return view('components.admin.datatable.actions', [
                            'buttons' => [
                                new ActionButton(
                                    route('admin.orders.edit', ['order' => $row->id]),
                                    __('admin.Edit'),
                                    'edit'
                                ),
                                new ActionButton(
                                    route('admin.orders.delete', ['order' => $row->id]),
                                    __('admin.Delete'),
                                    'delete'
                                ),
                            ],
                        ]);
                    }
                )
                ->html(),

        ];
    }
}
