<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\LabelIconDecorator;
use App\Models\Label;
use Illuminate\Support\Facades\Auth;
use Rappasoft\LaravelLivewireTables\Views\Column;

class LabelsDatatable extends BaseDatatable
{
    public $user_roles;
    protected $model = Label::class;

    public function configure(): void
    {
        $this->user_roles = Auth::user()->roles;
        $this->createLink = route('admin.labels.create');
        parent::configure();
    }

    public function columns(): array
    {
        return [
            Column::make(__('admin.ID'), 'id')
                ->sortable()
                ->collapseOnMobile(),
            Column::make(__('admin.Icon'), 'icon')
                ->sortable()
                ->collapseOnMobile()
                ->format(fn($value, Label $row, Column $column) => (new LabelIconDecorator())->decorate($value)),
            Column::make(__('admin.Name'), 'name')
                ->sortable()
                ->searchable(function ($query, $searchTerm) {
                    $query->whereRaw('LOWER(label.name) LIKE ?', ["%".strtolower($searchTerm)."%"]);
                }),
            Column::make(__('admin.Actions'), 'id')
                ->format(function ($value, Label $row, Column $column) {
                    $buttons = [
                        $this->prepareEditButton($row->id, 'id', 'labels'),
                    ];

                    if (auth()->user() && auth()->user()->hasRole('ROLE_ADMIN')) {
                        $buttons[] = $this->prepareDeleteButton($row->id, 'id', 'labels');
                    }

                    return view('components.admin.datatable.actions', [
                        'buttons' => $buttons,
                    ]);
                })
                ->html(),
        ];
    }
}
