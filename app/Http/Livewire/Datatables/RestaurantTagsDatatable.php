<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\TranslateValuesDecorator;
use App\Models\RestaurantTag;
use Rappasoft\LaravelLivewireTables\Views\Column;

class RestaurantTagsDatatable extends BaseDatatable
{
    protected $model = RestaurantTag::class;

    public function configure(): void
    {
        $this->createLink = route('admin.restaurant_tags.create');
        parent::configure();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable(),
            Column::make('Key', 'key')
                ->sortable()
                ->searchable(),
            Column::make('Value', 'value')
                ->sortable()
                ->html()
                ->format(fn ($value, RestaurantTag $row, Column $column) => (new TranslateValuesDecorator())->decorate($value)),
            Column::make('Actions', 'id')
                ->format(
                    function ($value, RestaurantTag $row, Column $column) {
                        return view('components.admin.datatable.actions', [
                            'buttons' => [
                                $this->prepareEditButton($row->id, 'restaurant_tag', 'restaurant_tags'),
                                $this->prepareDeleteButton($row->id, 'restaurant_tag', 'restaurant_tags'),
                            ],
                        ]);
                    }
                )
                ->html(),
        ];
    }
}
