<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\BoolStatusDecorator;
use App\Models\FoodCategory;
use App\Models\Resource;
use Bkwld\Croppa\Facades\Croppa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\ImageColumn;

class FoodCategoriesDatatable extends BaseDatatable
{
    public $user_roles;
    protected $model = FoodCategory::class;

    public function configure(): void
    {
        $this->user_roles = Auth::user()->roles;
        $this->createLink = route('admin.categories.create');
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
                ->searchable(),
//            ImageColumn::make(__('admin.Photo'))
//                ->location(
//                    function (FoodCategory $row) {
//                        $photo = Resource::query()
//                            ->where('resourcetable_type', 'food_categories')
//                            ->where('resourcetable_id', $row->id)
//                            ->first();
//
//                        if ($photo) {
//                            return Croppa::url($photo->getPhoto(true), null, 64);
//                        }
//
//                        return '';
//                    }
//                ),
            Column::make(__('admin.Description'), 'description')
                ->html()
                ->collapseOnMobile()
                ->format(fn($value, FoodCategory $row, Column $column) => Str::limit($value)),
            Column::make(__('admin.Position'), 'position')
                ->sortable()
                ->collapseOnMobile(),
            Column::make(__('admin.Visibility'), 'visibility')
                ->sortable()
                ->html()
                ->format(fn($value, FoodCategory $row, Column $column) => (new BoolStatusDecorator())->decorate($value)),
            Column::make(__('admin.Actions'), 'id')
                ->format(
                    function ($value, FoodCategory $row, Column $column) {
                        if ($row->name != 'Brak kategorii') {
                            return view('components.admin.datatable.actions', [
                                'buttons' => [
                                    $this->prepareEditButton($row->id, 'foodCategory', 'categories'),
                                    $this->prepareDeleteButton($row->id, 'foodCategory', 'categories'),
                                ],
                            ]);
                        }
                        return '';
                    }
                )
                ->html(),
        ];
    }
}
