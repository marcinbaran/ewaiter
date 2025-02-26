<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\BoolStatusDecorator;
use App\Decorators\LabelIconDecorator;
use App\Models\Attribute;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Rappasoft\LaravelLivewireTables\Views\Column;

class AttributesDatatable extends BaseDatatable
{
    protected $model = Attribute::class;

    public function configure(): void
    {
        $this->createLink = route('admin.attributes.create');
        $this->setAdditionalSelects(['*']);
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
                    fn ($value, Attribute $row, Column $column) => (new BoolStatusDecorator())->decorate($value)
                ),
            Column::make(__('admin.Icon'), 'icon')
                ->collapseOnMobile()
                ->format(fn ($value, Attribute $row, Column $column) => (new LabelIconDecorator())->decorate($value)),
            Column::make(__('admin.Name'), 'name')
                ->sortable()
                ->searchable(function ($query, $searchTerm) {
                    $query->whereRaw('LOWER(attributes.name) LIKE ?', ["%".strtolower($searchTerm)."%"]);
                }),
            Column::make(__('admin.attribute_group'), 'attribute_group_id')
                ->sortable()
                ->format(
                    fn ($value, Attribute $row, Column $column) => $row->attributeGroup?->name ?? '-'
                ),
            Column::make(__('admin.Created at'), 'created_at')
                ->sortable()
                ->format(
                    fn ($value, Attribute $row, Column $column) => Carbon::parse($row->created_at)->format('Y-m-d')
                )
                ->collapseOnMobile(),
            Column::make(__('admin.Actions'), 'id')
                ->format(
                    function ($value, Attribute $row, Column $column) {
                        return view('components.admin.datatable.actions', [
                            'buttons' => [
                                $this->prepareEditButton($row->id, 'attribute', 'attributes'),
                                $this->prepareDeleteButton($row->id, 'attribute', 'attributes'),
                            ],
                        ]);
                    }
                )
                ->html(),
        ];
    }

    public function bulkActions(): array
    {
        return [
            'bulkDelete' => __('admin.Delete'),
            'bulkCreateGroup' => __('attributes.create_group'),
        ];
    }

    public function bulkCreateGroup()
    {
        Redirect::route('admin.attribute_groups.create', ['attributes' => implode(',', $this->getSelected())]);
    }
}
