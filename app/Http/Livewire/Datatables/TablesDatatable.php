<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\BoolStatusDecorator;
use App\Models\Table;
use Rappasoft\LaravelLivewireTables\Views\Column;

class TablesDatatable extends BaseDatatable
{
    protected $model = Table::class;

    public function configure(): void
    {
        $this->createLink = route('admin.tables.create');
        parent::configure();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable(),
            Column::make(__('admin.Name'), 'name')
                ->sortable()
                ->searchable(),
            Column::make(__('admin.Number'), 'number')
                ->sortable()
                ->searchable(),
            Column::make(__('reservations.Number of people'), 'people_number')
                ->sortable(),
            Column::make(__('admin.Active'), 'active')
                ->sortable()
                ->format(fn ($value, Table $row, Column $column) => (new BoolStatusDecorator())->decorate($value)),
            Column::make(__('admin.Actions'), 'id')
                ->format(
                    function ($value, Table $row, Column $column) {
                        return view('components.admin.datatable.actions', [
                            'buttons' => [
                                $this->prepareEditButton($row->id, 'table', 'tables'),
                                $this->prepareDeleteButton($row->id, 'table', 'tables'),
                            ],
                        ]);
                    }
                )
                ->html(),

        ];
    }
}
