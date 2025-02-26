<?php

namespace App\Http\Livewire\Datatables;

use App\Models\ApiLog;
use Illuminate\Support\Str;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ApiLogsDatatable extends BaseDatatable
{
    protected $model = ApiLog::class;

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
            Column::make(__('User'), 'user_id')
                ->sortable()
                ->searchable()
                ->format(
                    fn ($value, ApiLog $row, Column $column) => $row->user ? '['.$row->user->id.'] '.$row->user?->login : 'Guest'
                ),
            Column::make(__('Method'), 'method')
                ->sortable()
                ->searchable(),
            Column::make(__('URL'), 'full_url')
                ->sortable()
                ->searchable(),
            Column::make(__('Parameters'), 'request_body')
                ->format(
                    fn ($value, ApiLog $row, Column $column) => Str::limit($value)
                ),

        ];
    }
}
