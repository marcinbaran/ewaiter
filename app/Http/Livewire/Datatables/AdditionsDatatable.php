<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\MoneyDecorator;
use App\Models\Addition;
use Carbon\Carbon;
use Rappasoft\LaravelLivewireTables\Views\Column;

class AdditionsDatatable extends BaseDatatable
{
    protected $model = Addition::class;

    public function configure(): void
    {
        $this->createLink = route('admin.additions.create');
        parent::configure();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()
                ->collapseOnMobile(),
            Column::make(__('admin.Name'), 'name')
                ->sortable()
                ->searchable(function ($query, $searchTerm) {
                    $query->whereRaw('LOWER(additions.name) LIKE ?', ["%".strtolower($searchTerm)."%"]);
                }),
            Column::make(__('admin.Price'), 'price')
                ->sortable()
                ->format(
                    fn ($value, Addition $row, Column $column) => (new MoneyDecorator())->decorate($value, 'PLN')
                ),
            Column::make(__('admin.Created at'), 'created_at')
                ->sortable()
                ->format(
                    fn ($value, Addition $row, Column $column) => Carbon::parse($row->created_at)->format('Y-m-d')
                )
                ->collapseOnMobile(),
            Column::make(__('admin.Actions'), 'id')
                ->format(
                    function ($value, Addition $row, Column $column) {
                        return view('components.admin.datatable.actions', [
                            'buttons' => [
                                $this->prepareEditButton($row->id, 'addition', 'additions'),
                                $this->prepareDeleteButton($row->id, 'addition', 'additions'),
                            ],
                        ]);
                    }
                )
                ->html(),

        ];
    }
}
