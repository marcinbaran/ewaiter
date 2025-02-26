<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\Editable\Commission\CommentEditableDecorator;
use App\Decorators\Editable\Commission\StatusEditableDecorator;
use App\Decorators\MoneyDecorator;
use App\Exports\CommissionsExport;
use App\Models\Commission;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\Views\Column;

class CommissionsDatatable extends BaseDatatable
{
    protected $model = Commission::class;

    public function configure(): void
    {
        $this->createLink = false;
        parent::configure();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()
                ->collapseOnMobile(),
            Column::make(__('commissions.date'), 'issued_at')
                ->sortable(),
            Column::make(__('commissions.restaurant'), 'restaurant_name')
                ->sortable()
                ->searchable(),
            Column::make(__('commissions.bill_id'), 'bill_id')
                ->sortable(),
            Column::make(__('commissions.bill_price'), 'bill_price')
                ->sortable()
                ->html()
                ->format(
                    fn ($value, Commission $row, Column $column) => (new MoneyDecorator())->decorate($value)
                ),
            Column::make(__('commissions.commission'), 'commission')
                ->sortable()
                ->html()
                ->format(
                    fn ($value, Commission $row, Column $column) => (new MoneyDecorator())->decorate($value)
                ),
            Column::make(__('commissions.status'), 'status')
                ->sortable()
                ->html()
                ->format(
                    fn ($value, Commission $row, Column $column) => StatusEditableDecorator::create($row, $value)->decorate()
                ),
            Column::make(__('commissions.comment'), 'comment')
                ->sortable()
                ->searchable()
                ->html()
                ->format(
                    fn ($value, Commission $row, Column $column) => CommentEditableDecorator::create($row, $value)->decorate()
                ),
        ];
    }

    public function bulkActions(): array
    {
        return [
            'bulkSetStatusActive' => __('commissions.set-status-active'),
            'bulkSetStatusFinished' => __('commissions.set-status-finished'),
            'bulkSetStatusCancelled' => __('commissions.set-status-cancelled'),
            'bulkExportToSpreadsheet' => __('commissions.export-to-spreadsheet'),
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|void
     */
    public function bulkExportToSpreadsheet()
    {
        $commissions = Commission::select('id', 'issued_at', 'restaurant_name', 'bill_id', 'bill_price', 'commission', 'status', 'comment')->whereIn('id', $this->getSelected())->get();

        if (count($commissions) > 0) {
            return Excel::download(new CommissionsExport($commissions), 'commissions-'.date('d-m-Y').'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }
    }

    public function bulkSetStatusActive(): void
    {
        foreach ($this->getSelected() as $item) {
            $commission = Commission::find($item);
            $commission->status = 'active';
            $commission->save();
        }
    }

    public function bulkSetStatusFinished(): void
    {
        foreach ($this->getSelected() as $item) {
            $commission = Commission::find($item);
            $commission->status = 'finished';
            $commission->save();
        }
    }

    public function bulkSetStatusCancelled(): void
    {
        foreach ($this->getSelected() as $item) {
            $commission = Commission::find($item);
            $commission->status = 'canceled';
            $commission->save();
        }
    }
}
