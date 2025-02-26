<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\Dashboard\RestaurantPhotoWithStatusDecorator;
use App\Http\Livewire\ReportModal;
use App\Models\Report;
use App\Models\Restaurant;
use App\Services\Datatable\ActionButton;
use App\Services\RestaurantReportService;
use Carbon\Carbon;
use Rappasoft\LaravelLivewireTables\Views\Column;

class RestaurantDashboardDatatable extends BaseDatatable
{
    public $isReportModalOpen = false;
    public ?int $reportModalRestaurantId = null;
    public ?int $duration = null;

    protected $model = Report::class;

    public function configure(): void
    {
        parent::configure();
        $this->setAdditionalSelects(['*']);
        $this->setConfigurableAreas($this->prepareAreas());
    }

    private function prepareAreas(): array
    {
        return [
            'before-tools' => 'admin.partials.table.report-modal',
            'toolbar-right-end' => [
                'components.admin.datatable.button',
                [
                    'type' => 'refresh',
                    'wireClick' => 'refreshDatatable',
                ],
            ],
        ];
    }
    public function columns(): array
    {
        return [
            Column::make(__('dashboard._datatable.id'), 'id')
                ->sortable()
                ->searchable()
                ->collapseOnMobile(),
            Column::make(__('dashboard._datatable.restaurant'), 'restaurant_id')
                ->sortable()
                ->searchable()
                ->format(
                    fn($value, Report $row, Column $column) => $this->getRestaurantWithPhotoAndStatus($row->restaurant)
                )
                ->html(),
            Column::make(__('dashboard._datatable.last_activity'), 'id')
                ->format(
                    fn($value, Report $row, Column $column) => $row->restaurant->last_activity_request_date
                ),
            Column::make(__('dashboard._datatable.orders'), 'id')
                ->format(
                    fn($value, Report $row, Column $column) => $this->getBillsCount($row->restaurant)
                ),
            Column::make(__('dashboard._datatable.provision'), 'id')
                ->format(
                    fn($value, Report $row, Column $column) => $this->getProvisionValue($row->restaurant)
                ),
            Column::make(__('dashboard._datatable.actions'), 'restaurant_id')
                ->format(
                    function ($value, Report $row, Column $column) {
                        return view('components.admin.datatable.actions', [
                            'buttons' => [
                                $this->prepareReportModalButton($value),
                            ],
                        ]);
                    }
                )
                ->html(),
        ];
    }

    private function getRestaurantWithPhotoAndStatus(Restaurant $restaurant): ?string
    {
        $isActive = $restaurant->last_activity_request_date
            ? Carbon::make($restaurant->last_activity_request_date)->greaterThanOrEqualTo(Carbon::now()->subMinutes(1))
            : false;

        return (new RestaurantPhotoWithStatusDecorator())->decorate(
            $restaurant->photo?->getPhoto() ?? '',
            $restaurant->name,
            $restaurant->name,
            $isActive
        );
    }

    private function getBillsCount(Restaurant $restaurant): int
    {
        return count($this->getReportData($restaurant)['bills']);
    }

    private function getReportData(Restaurant $restaurant): array
    {
        return (new RestaurantReportService('pdf'))->prepareData($restaurant)->toArray();
    }

    private function getProvisionValue(Restaurant $restaurant): string
    {
        $reportData = $this->getReportData($restaurant);

        $currency = isset($reportData['bills'][0]) ? $reportData['bills'][0]['currency'] : 'zł';
        $provision = $reportData['totals']['provision'];

        return $provision . ' ' . $currency;
    }

    private function prepareReportModalButton(int $restaurantId): ActionButton
    {
        return new ActionButton(route('admin.report.index', ['restaurantId' => $restaurantId]), __('dashboard._datatable.download-report'), 'report-modal', $restaurantId);
    }

    public function refreshDatatable(): void
    {
        $this->emit('refreshDatatable');
    }

    public function showReportModal(int $restaurantId = null): void
    {
        if ($restaurantId) {
            $this->reportModalRestaurantId = $restaurantId;
            $this->isReportModalOpen = true;
        }
    }

    public function closeReportModal(): void
    {
        $this->isReportModalOpen = false;
    }

    public function downloadReport(): void
    {
        if ($this->reportModalRestaurantId) {
            redirect()->route('admin.report.index', ['restaurantId' => $this->reportModalRestaurantId, 'duration' => $this->duration]);
        }

        $this->closeReportModal();
    }

    public function sendReport(): void
    {
        if ($this->reportModalRestaurantId) {
            redirect()->route('admin.report.send.index', ['restaurantId' => $this->reportModalRestaurantId, 'duration' => $this->duration]);
        }

        $this->closeReportModal();
    }
}
