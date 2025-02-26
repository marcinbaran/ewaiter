<?php

namespace App\Http\Livewire\Datatables;

use App\Models\Room;
use Rappasoft\LaravelLivewireTables\Views\Column;

class RoomsDatatable extends BaseDatatable
{
    protected $model = Room::class;

    public function configure(): void
    {
        $this->createLink = route('admin.rooms.create');
        parent::configure();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable(),
            Column::make(__('admin.Name'), 'name')
                ->sortable()
                ->searchable(),
            Column::make(__('admin.Number'), 'number')
                ->sortable()
                ->searchable(),
            Column::make(__('admin.Floor'), 'floor')
                ->sortable(),
            Column::make(__('admin.Actions'), 'id')
                ->format(
                    function ($value, Room $row, Column $column) {
                        return view('components.admin.datatable.actions', [
                            'buttons' => [
                                $this->prepareEditButton($row->id, 'room', 'rooms'),
                                $this->prepareDeleteButton($row->id, 'room', 'rooms'),
                            ],
                        ]);
                    }
                )
                ->html(),

        ];
    }
}
