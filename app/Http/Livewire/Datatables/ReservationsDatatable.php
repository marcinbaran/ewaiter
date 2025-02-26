<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\ReservationStatusDecorator;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ReservationsDatatable extends BaseDatatable
{
    protected $model = Reservation::class;

    public function configure(): void
    {
        $this->createLink = route('admin.reservations.create');
        parent::configure();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable(),
            Column::make(__('admin.reservations.status'), 'status')
                ->sortable()
                ->collapseOnMobile()
                ->format(fn ($value, Reservation $row, Column $column) => (new ReservationStatusDecorator)->decorate($row)),
            Column::make(__('admin.reservations.user'), 'name')
                ->sortable()
                ->searchable(),
            Column::make(__('admin.reservations.phone'), 'phone')
                ->sortable()
                ->searchable(),
            Column::make(__('admin.reservations.people_count'), 'people_number')
                ->sortable(),
            Column::make(__('admin.reservations.start_date'), 'start')
                ->sortable()
                ->collapseOnMobile()
                ->searchable(),
            Column::make(__('admin.Actions'), 'id')
                ->format(
                    function ($value, Reservation $row, Column $column) {
                        return view('components.admin.datatable.actions', [
                            'buttons' => [
                                $this->prepareEditButton($row->id, 'reservation', 'reservations'),
                                $this->prepareDeleteButton($row->id, 'reservation', 'reservations'),
                            ],
                        ]);
                    }
                )
                ->html(),

        ];
    }

    public function builder(): \Illuminate\Database\Eloquent\Builder
    {
        return Reservation::query()
            ->orderByRaw('CASE WHEN status = 0 THEN 0 ELSE 1 END ASC')
            ->orderByRaw('CASE WHEN status = 0 THEN created_at ELSE NULL END ASC')
            ->orderByRaw('CASE WHEN status != 0 THEN created_at ELSE NULL END DESC');
    }
}
