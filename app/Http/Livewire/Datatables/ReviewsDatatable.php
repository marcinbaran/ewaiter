<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\StarDecorator;
use App\Http\Filters\Bill\CreatedAtTo;
use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ReviewsDatatable extends BaseDatatable
{

    protected $model = Review::class;

    public function configure(): void
    {
        $this->createLink = false;
        $this->setFilterLayoutSlideDown();
        $this->setSingleSortingDisabled();
        $this->setDefaultSort('created_at', 'desc');
        $this->setAdditionalSelects(['reviews.*', 'users.login as user_login',]);
        parent::configure();
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make(__('admin.reviewsTable.created_at'), "created_at")
                ->sortable(),
            Column::make(__('admin.reviewsTable.rating_food'), "rating_food")
                ->sortable()
                ->format(function ($value, Review $row, Column $column): string {
                    return StarDecorator::decorate($row->rating_food);
                })->html(),
            Column::make(__('admin.reviewsTable.rating_delivery'), "rating_delivery")
                ->sortable()
                ->format(function ($value, Review $row, Column $column): string {
                    return StarDecorator::decorate($row->rating_delivery);
                })->html(),
            Column::make(__('admin.reviewsTable.user_login'), "user.login")
                ->sortable()
                ->format(function ($value, Review $row, Column $column): string {
                    return $row->user_login;
                }),
            Column::make(__('admin.reviewsTable.bill_id'), "bill_id")
                ->sortable()
                ->format(function ($value, Review $row, Column $column): string {
                    return $row->bill_id;
                }),
            Column::make(__('admin.Actions'), 'id')
                ->format(
                    function ($value, Review $row, Column $column) {
                        return view('components.admin.datatable.actions', [
                            'buttons' => [
                                $this->prepareShowButton($row->id, 'review', 'reviews'),
                            ],
                        ]);
                    }
                )
        ];
    }

    public function filters(): array
    {
        return [
            (new CreatedAtTo())->prepare(),

        ];
    }

    public function builder(): Builder
    {
        $restaurant = Restaurant::getCurrentRestaurant();
        $restaurantId = $restaurant->id;

        return Review::query()
            ->select('reviews.*', 'users.login as user_login')
            ->join('users', 'reviews.user_id', '=', 'users.id')
            ->where('reviews.restaurant_id', $restaurant->id)
            ->where('users.first_name', '!=', 'deleted_user');
    }


}
