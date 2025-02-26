<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\BoolStatusDecorator;
use App\Decorators\MoneyDecorator;
use App\Models\Voucher;
use Carbon\Carbon;
use Rappasoft\LaravelLivewireTables\Views\Column;

class VoucherDatatable extends BaseDatatable
{
    protected $model = Voucher::class;

    public function configure(): void
    {
        $this->createLink = route('admin.vouchers.create');
        $this->setAdditionalSelects(['*']);
        parent::configure();
    }

    public function columns(): array
    {
        return [
            Column::make(__('voucher.datatable.id'), 'id')
                ->sortable(),
            Column::make(__('voucher.datatable.comment'), 'comment')
                ->sortable()
                ->searchable(),
            Column::make(__('voucher.datatable.code'), 'code')
                ->sortable()
                ->format(fn ($value, Voucher $row, Column $column) => '<span class="flex items-center">'.(new BoolStatusDecorator())->decorate($row->is_used).' '.$value.'</span>')
                ->html(),
            Column::make(__('voucher.datatable.value'), 'value')
                ->sortable()
                ->format(fn ($value, Voucher $row, Column $column) => (new MoneyDecorator())->decorate($value)),
            Column::make(__('voucher.datatable.used_by'), 'used_by')
                ->sortable()
                ->format(fn ($value, Voucher $row, Column $column) => $value ? $row->user->email : '-'),
            Column::make(__('voucher.datatable.used_at'), 'used_at')
                ->sortable()
                ->format(fn ($value, Voucher $row, Column $column) => $value ? Carbon::parse($value)->format('Y-m-d H:i') : '-'),
            Column::make(__('voucher.datatable.actions'), 'id')
                ->format(
                    function ($value, Voucher $row, Column $column) {
                        $buttons = [];
                        if (! $row->is_used) {
                            $buttons[] = $this->prepareEditButton($row->id, 'voucher', 'vouchers');
                            $buttons[] = $this->prepareDeleteButton($row->id, 'voucher', 'vouchers');
                        }

                        return view('components.admin.datatable.actions', [
                            'buttons' => $buttons,
                        ]);
                    }
                )
                ->html(),
        ];
    }
}
