<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\AttributeGroups\InputTypeDecorator;
use App\Decorators\BoolStatusDecorator;
use App\Enum\AttributeGroupInputType;
use App\Models\AttributeGroup;
use Carbon\Carbon;
use Rappasoft\LaravelLivewireTables\Views\Column;

class AttributeGroupsDatatable extends BaseDatatable
{
    protected $model = AttributeGroup::class;

    public function configure(): void
    {
        $this->createLink = route('admin.attribute_groups.create');
        parent::configure();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()
                ->collapseOnMobile(),
            Column::make(__('admin.activeness'), 'is_active')
                ->sortable()
                ->format(
                    fn ($value, AttributeGroup $row, Column $column) => (new BoolStatusDecorator())->decorate($value)
                ),
            Column::make(__('admin.main'), 'is_primary')
                ->sortable()
                ->format(
                    fn ($value, AttributeGroup $row, Column $column) => (new BoolStatusDecorator())->decorate($value)
                ),
            Column::make(__('admin.Name'), 'name')
                ->sortable()
                ->searchable(function ($query, $searchTerm) {
                    $query->whereRaw('LOWER(attribute_groups.name) LIKE ?', ["%".strtolower($searchTerm)."%"]);
                }),
            Column::make(__('admin.Type'), 'input_type')
                ->sortable()
                ->format(
                    fn ($value, AttributeGroup $row, Column $column) => (new InputTypeDecorator())->decorate(AttributeGroupInputType::from($value))
                ),
            Column::make(__('admin.Created at'), 'created_at')
                ->sortable()
                ->format(
                    fn ($value, AttributeGroup $row, Column $column) => Carbon::parse($row->created_at)->format('Y-m-d')
                )
                ->collapseOnMobile(),
            Column::make(__('admin.Actions'), 'id')
                ->format(
                    function ($value, AttributeGroup $row, Column $column) {
                        return view('components.admin.datatable.actions', [
                            'buttons' => [
                                $this->prepareEditButton($row->id, 'attribute_group', 'attribute_groups'),
                                $this->prepareDeleteButton($row->id, 'attribute_group', 'attribute_groups'),
                            ],
                        ]);
                    }
                )
                ->html(),

        ];
    }
}
