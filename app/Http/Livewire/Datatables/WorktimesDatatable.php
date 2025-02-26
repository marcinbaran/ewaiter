<?php

namespace App\Http\Livewire\Datatables;

use App\Models\Worktime;
use Carbon\Carbon;
use Rappasoft\LaravelLivewireTables\Views\Column;

class WorktimesDatatable extends BaseDatatable
{
    protected $model = Worktime::class;

    public function configure(): void
    {
        $this->createLink = route('admin.worktimes.create');
        parent::configure();
    }

    public function columns(): array
    {
        return [
            Column::make(__('admin.ID'), 'id')
                ->sortable(),
            Column::make(__('worktime.Date'), 'date')
                ->sortable()
                ->searchable()
                ->format(fn ($value, Worktime $row, Column $column) => Carbon::parse($row->date)->format('Y-m-d')),
            Column::make(__('worktime.Start'), 'start')
                ->sortable(),
            Column::make(__('worktime.End'), 'end')
                ->sortable(),
            Column::make(__('worktime.Type'), 'type')
                ->sortable()
                ->format(fn ($value, Worktime $row, Column $column) => __('worktime.'.$row->getTypeName())),
            Column::make(__('admin.Actions'), 'id')
                ->format(
                    function ($value, Worktime $row, Column $column) {
                        return view('components.admin.datatable.actions', [
                            'buttons' => [
                                $this->prepareEditButton($row->id, 'worktime', 'worktimes'),
                                $this->prepareDeleteButton($row->id, 'worktime', 'worktimes'),
                            ],
                        ]);
                    }
                )
                ->html(),

        ];
    }
}
