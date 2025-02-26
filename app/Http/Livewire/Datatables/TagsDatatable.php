<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\BoolStatusDecorator;
use App\Decorators\TagIconDecorator;
use App\Models\Tag;
use Rappasoft\LaravelLivewireTables\Views\Column;

class TagsDatatable extends BaseDatatable
{
    protected $model = Tag::class;

    public function configure(): void
    {
        $this->createLink = route('admin.tags.create');
        parent::configure();
    }

    public function columns(): array
    {
        return [
            Column::make(__('admin.ID'), 'id')
                ->sortable()
                ->collapseOnMobile(),
            Column::make(__('admin.Icon'), 'icon')
                ->sortable()
                ->collapseOnMobile()
                ->format(fn($value, Tag $row, Column $column) => (new TagIconDecorator())->decorate($value)),
            Column::make(__('admin.Name'), 'name')
                ->sortable()
                ->searchable(function ($query, $searchTerm) {
                    $query->whereRaw('LOWER(tags.name) LIKE ?', ["%".strtolower($searchTerm)."%"]);
                }),
            Column::make(__('admin.Visibility'), 'visibility')
                ->sortable()
                ->collapseOnMobile()
                ->format(fn($value, Tag $row, Column $column) => (new BoolStatusDecorator())->decorate($value)),
            Column::make(__('admin.Actions'), 'id')
                ->format(
                    function ($value, Tag $row, Column $column) {
                        return view('components.admin.datatable.actions', [
                            'buttons' => [
                                $this->prepareEditButton($row->id, 'tag', 'tags'),
                                $this->prepareDeleteButton($row->id, 'tag', 'tags'),
                            ],
                        ]);
                    }
                )
                ->html(),
        ];
    }
}
