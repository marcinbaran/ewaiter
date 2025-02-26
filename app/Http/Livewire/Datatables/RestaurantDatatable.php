<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\BoolStatusDecorator;
use App\Models\ResourceSystem;
use App\Models\Restaurant;
use App\Services\Datatable\ActionButton;
use Bkwld\Croppa\Facades\Croppa;
use Carbon\Carbon;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\ImageColumn;

class RestaurantDatatable extends BaseDatatable
{
    protected $model = Restaurant::class;

    public function bulkActions(): array
    {
        return [
            'setVisible' => __('admin.Set visible'),
            'setInvisible' => __('admin.Set invisible'),
        ];
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()
                ->setSortingPillDirections('Asc', 'Desc'),
            ImageColumn::make(__('admin.Logo'))
                ->location(
                    function (Restaurant $row) {
                        $photos = ResourceSystem::query()
                            ->where('resourcetable_type', 'restaurants')
                            ->where('resourcetable_id', $row->id)
                            ->get();

                        foreach ($photos as $photo) {
                            $imageType = $photo->additional['file_type'] ?? '';
                            if ($imageType == 'logo') {
                                return Croppa::url($photo->getPhoto(true), null, 34);
                            }
                        }

                        return '';
                    }
                ),
            Column::make('Name')
                ->sortable()
                ->searchable(),
            Column::make('Hostname')
                ->sortable()
                ->searchable(),
            Column::make(__('admin.Created at'), 'created_at')
                ->sortable()
                ->format(
                    fn ($value, Restaurant $row, Column $column) => Carbon::parse($row->created_at)->format('Y-m-d')
                )
                ->setSortingPillDirections('Asc', 'Desc'),
            Column::make(__('admin.Provision'), 'provision')
                ->sortable()
                ->collapseOnMobile()
                ->setSortingPillDirections('Asc', 'Desc')
                ->format(
                    fn ($value, Restaurant $row, Column $column) => intval($value).' %'
                ),
            Column::make(__('admin.Visibility'), 'visibility')
                ->sortable()
                ->html()
                ->format(
                    fn ($value, Restaurant $row, Column $column) => (new BoolStatusDecorator())->decorate($value)
                ),
            Column::make(__('admin.Actions'), 'id')
                ->format(
                    function ($value, Restaurant $restaurant, Column $column) {
                        return view('components.admin.datatable.actions', [
                            'buttons' => [
                                new ActionButton(
                                    route('admin.restaurants.login', ['restaurant' => $restaurant->id]),
                                    __('admin.Login'),
                                    'login',
                                    $restaurant->id
                                ),
                                $this->prepareEditButton($restaurant->id, 'restaurant', 'restaurants'),
                                $this->prepareDeleteButton($restaurant->id, 'restaurant', 'restaurants'),
                            ],
                        ]);
                    }
                )
                ->html(),

        ];
    }

    public function configure(): void
    {
        $this->createLink = route('admin.restaurants.create');
        parent::configure();

        $this->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {
            if (! $column->isField($this->boldColumn)) {
                return [
                    'default' => false,
                    'class' => 'px-6 py-4 whitespace-nowrap text-sm font-light dark:text-white',
                ];
            }

            return [];
        });
    }

    public function setVisible()
    {
        Restaurant::query()->whereIn('id', $this->getSelected())->update(['visibility' => 1]);

        $this->clearSelected();
        $this->emit('refreshDatatable');
    }

    public function setInvisible()
    {
        Restaurant::query()->whereIn('id', $this->getSelected())->update(['visibility' => 0]);

        $this->clearSelected();
        $this->emit('refreshDatatable');
    }
}
