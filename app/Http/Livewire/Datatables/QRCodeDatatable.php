<?php

namespace App\Http\Livewire\Datatables;

use App\Models\QRCode;
use Rappasoft\LaravelLivewireTables\Views\Column;

class QRCodeDatatable extends BaseDatatable
{
    protected $model = QRCode::class;

    public function configure(): void
    {
        $this->createLink = route('admin.qr_codes.create');
        parent::configure();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()
                ->collapseOnMobile(),
            Column::make(__('admin.Object type'), 'object_type')
                ->sortable()
                ->searchable(),
            Column::make(__('admin.Object id'), 'object_id')
                ->sortable()
                ->searchable(),
            Column::make(__('admin.URL'), 'url')
                ->sortable()
                ->collapseOnMobile(),
            Column::make(__('admin.QR Code'), 'path_qrcode')
                ->sortable()
                ->html()
                ->collapseOnMobile()
                ->format(fn ($value, QRCode $row, Column $column) => '<img style="max-width: 60px;" src="/'.$row->getPath().'" />'),
            Column::make(__('admin.Actions'), 'id')
                ->format(
                    function ($value, QRCode $row, Column $column) {
                        return view('components.admin.datatable.actions', [
                            'buttons' => [
                                $this->prepareDeleteButton($row->id, 'qr_code', 'qr_codes'),
                            ],
                        ]);
                    }
                )
                ->html(),

        ];
    }
}
