<?php

namespace App\Http\Livewire\Datatables;

use App\Models\ChangeLog;
use Rappasoft\LaravelLivewireTables\Views\Column;

class LogsDatatable extends BaseDatatable
{
    protected $model = ChangeLog::class;

    public function configure(): void
    {
        $this->createLink = false;
        parent::configure();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable(),
            Column::make(__('admin.Action'), 'action')
                ->sortable(),
            Column::make(__('Model'), 'model')
                ->sortable()
                ->searchable(),
            Column::make(__('Element ID'), 'element_id')
                ->sortable()
                ->searchable(),
            Column::make(__('User'), 'user_id')
                ->sortable()
                ->searchable()
                ->format(
                    fn ($value, ChangeLog $row, Column $column) => '['.$row->user_id.'] '.$row->user_name
                ),

        ];
    }
}
