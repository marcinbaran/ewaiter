<?php

namespace App\Http\Livewire\Datatables;

use App\Models\OnlinePaymentProviderAccount;
use Rappasoft\LaravelLivewireTables\Views\Column;

class OnlinePaymentProviderAccountDatatable extends BaseDatatable
{
    protected $model = OnlinePaymentProviderAccount::class;

    public function configure(): void
    {
        $this->createLink = route('admin.online_payment_provider_account.create');
        parent::configure();
    }

    public function columns(): array
    {
        return [
            Column::make(__('online_payment_provider_account.datatable.id'), 'id')
                ->sortable()
                ->collapseOnMobile(),
            Column::make(__('online_payment_provider_account.datatable.restaurant'), 'restaurant_id')
                ->sortable()
                ->searchable()
                ->format(
                    fn ($value, OnlinePaymentProviderAccount $row, Column $column) => $row->restaurant->name
                )
                ->html(),
            Column::make(__('online_payment_provider_account.datatable.comment'), 'comment')
                ->sortable()
                ->searchable(),
            Column::make(__('online_payment_provider_account.datatable.actions'), 'id')
                ->format(
                    function ($value, OnlinePaymentProviderAccount $row, Column $column) {
                        $buttons = [
                            $this->prepareDeleteButton($row->id, 'online_payment_provider_account', 'online_payment_provider_account'),
                        ];

                        return view('components.admin.datatable.actions', [
                            'buttons' => $buttons,
                        ]);
                    }
                )
                ->html(),
        ];
    }
}
