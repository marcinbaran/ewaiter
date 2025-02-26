<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\BoolStatusDecorator;
use App\Models\User;
use Carbon\Carbon;
use Hyn\Tenancy\Facades\TenancyFacade;
use Rappasoft\LaravelLivewireTables\Views\Column;

class SystemUsersDatatable extends BaseDatatable
{
    protected $model = User::class;

    protected $boldColumn = 'first_name';

    public function configure(): void
    {
        $this->createLink = route('admin.users.create');
        parent::configure();

        $this->setAdditionalSelects(['users.last_name as last_name']);
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()
                ->collapseOnMobile(),
            Column::make(__('admin.Name'), 'first_name')
                ->sortable()
                ->searchable()
                ->format(fn ($value, User $user, Column $column) => $user->first_name.' '.$user->last_name),
            Column::make(__('admin.Login'), 'login')
                ->sortable()
                ->searchable()
                ->collapseOnMobile(),
            Column::make(__('admin.email'), 'email')
                ->sortable()
                ->searchable(),
            Column::make(__('admin.Points'), 'id')
                ->sortable()
                ->collapseOnMobile()
                ->hideIf(! TenancyFacade::website())
                ->format(fn ($value, User $user, Column $column) => $user->reklamy_referring_user?->wallet->balance),
            Column::make(__('admin.Activated'), 'activated')
                ->sortable()
                ->format(fn ($value, User $user, Column $column) => (new BoolStatusDecorator())->decorate($value)),
            Column::make(__('admin.Blocked'), 'blocked')
                ->sortable()
                ->collapseOnMobile()
                ->format(fn ($value, User $user, Column $column) => (new BoolStatusDecorator())->decorate($value)),
            Column::make(__('admin.Created at'), 'created_at')
                ->sortable()
                ->collapseOnMobile()
                ->format(
                    fn ($value, User $row, Column $column) => Carbon::parse($row->created_at)->format('Y-m-d')
                ),
            Column::make(__('admin.Actions'), 'id')
                ->format(
                    function ($value, User $user, Column $column) {
                        return view('components.admin.datatable.actions', [
                            'buttons' => [
                                $this->prepareEditButton($user->id, 'user', 'users'),
                                $this->prepareDeleteButton($user->id, 'user', 'users'),
                            ],
                        ]);
                    }
                )
                ->html(),

        ];
    }
}
