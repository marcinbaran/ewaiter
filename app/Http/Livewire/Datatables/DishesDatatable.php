<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\BoolStatusDecorator;
use App\Decorators\DishGalleryDecorator;
use App\Decorators\MoneyDecorator;
use App\Http\Filters\Bill\DishCategory;
use App\Models\Dish;
use App\Models\Promotion;
use App\Models\PromotionDish;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\Views\Column;

class DishesDatatable extends BaseDatatable
{
    protected $model = Dish::class;

    public function configure(): void
    {
        $this->createLink = route('admin.dishes.create');
        parent::configure();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()
                ->collapseOnMobile(),
            Column::make(__('admin.Photo'), 'id')
                ->collapseOnMobile()
                ->format(fn($value, Dish $row, Column $column) => (new DishGalleryDecorator())->decorate($row)),
            Column::make(__('admin.Name'), 'name')
                ->sortable()
                ->searchable(function ($query, $searchTerm) {
                    $query->whereRaw('LOWER(dishes.name) LIKE ?', ["%".strtolower($searchTerm)."%"]);
                }),
            Column::make(__('admin.file format'), 'id')
                ->html()
                ->collapseOnMobile()
                ->format(function ($value, Dish $row, Column $column) {
                    $firstPhoto = $row->photos()->first();

                    if (null == $firstPhoto?->getPhoto()) {
                        return '';
                    }

                    return pathinfo(public_path($firstPhoto->getPhoto()))['extension'] ?? '-';
                }),
            Column::make(__('admin.Price'), 'price')
                ->sortable()
                ->format(fn($value, Dish $row, Column $column) => (new MoneyDecorator())->decorate($value, 'PLN')),
            Column::make(__('admin.Category'), 'category.name')
                ->format(function ($value, Dish $row, Column $column) {
                    $nameJSon = json_decode($value);
                    $locale = app()->getLocale();
                    $fallbackLocale = config('app.fallback_locale');
                    if (!isset($nameJSon->$locale)) {
                        return $nameJSon->$fallbackLocale;
                    } else if (isset($nameJSon->$locale)) {
                        return $nameJSon->$locale;
                    } else {
                        return "-";
                    }

                }),
            Column::make(__('admin.Position'), 'position')
                ->collapseOnMobile(),
            Column::make(__('admin.Visibility'), 'visibility')
                ->collapseOnMobile()
                ->format(fn($value, Dish $row, Column $column) => (new BoolStatusDecorator())->decorate($value)),
            Column::make(__('admin.Actions'), 'id')
                ->format(
                    function ($value, Dish $row, Column $column) {
                        return view('components.admin.datatable.actions', [
                            'buttons' => [
                                $this->prepareEditButton($row->id, 'dish', 'dishes'),
                                $this->prepareDeleteButton($row->id, 'dish', 'dishes'),
                            ],
                        ]);
                    }
                )
                ->html(),

        ];
    }

    public function filters(): array
    {
        return [
            (new DishCategory())->prepare(),
        ];
    }

    public function confirmDelete()
    {
        DB::connection('tenant')->transaction(function () {
            Promotion::where('order_dish_id', $this->deleteId)->update(['active' => 0]);

            $allPromotionsWithDeletingDish = PromotionDish::where('dish_id', $this->deleteId)->get();

            Promotion::whereIn('id', $allPromotionsWithDeletingDish->pluck('promotion_id'))->update(['active' => 0]);
        });

        parent::confirmDelete();
    }
}
