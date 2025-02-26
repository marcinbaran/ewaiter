<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\BoolStatusDecorator;
use App\Models\AdditionGroup;
use Carbon\Carbon;
use Rappasoft\LaravelLivewireTables\Views\Column;

class AdditionGroupsDatatable extends BaseDatatable
{
    protected $model = AdditionGroup::class;

    public function configure(): void
    {
        $this->createLink = route('admin.additions_groups.create');
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
                    $query->whereRaw('LOWER(additions_groups.name) LIKE ?', ["%".strtolower($searchTerm)."%"]);
                }),
            Column::make(__('admin.Type'), 'type')
                ->sortable()
                ->format(
                    fn ($value, AdditionGroup $row, Column $column) => __('admin.'.$row->getTypeName())
                ),
            Column::make(__('admin.Visibility'), 'visibility')
                ->sortable()
                ->format(
                    fn ($value, AdditionGroup $row, Column $column) => (new BoolStatusDecorator())->decorate($value)
                ),
            Column::make(__('admin.Created at'), 'created_at')
                ->sortable()
                ->format(
                    fn ($value, AdditionGroup $row, Column $column) => Carbon::parse($row->created_at)->format('Y-m-d')
                )
                ->collapseOnMobile(),
            Column::make(__('admin.Actions'), 'id')
                ->format(
                    function ($value, AdditionGroup $row, Column $column) {
                        return view('components.admin.datatable.actions', [
                            'buttons' => [
                                $this->prepareDuplicateButton($row->id, 'addition_group', 'additions_groups'),
                                $this->prepareEditButton($row->id, 'addition_group', 'additions_groups'),
                                $this->prepareDeleteButton($row->id, 'addition_group', 'additions_groups'),
                            ],
                        ]);
                    }
                )
                ->html(),

        ];
    }
}
