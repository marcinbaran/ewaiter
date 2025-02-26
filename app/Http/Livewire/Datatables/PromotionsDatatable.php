<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\BoolStatusDecorator;
use App\Decorators\DatePeriodDecorator;
use App\Enum\PromotionType;
use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PromotionsDatatable extends BaseDatatable
{
    protected $model = Promotion::class;

    public function configure(): void
    {
        $this->createLink = route('admin.promotions.create.dish');
        parent::configure();
        $this->setEagerLoadAllRelationsStatus(true);
        $this->setAdditionalSelects(['*']);
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()
                ->collapseOnMobile(),
            Column::make(__('admin.Name'), 'name')
                ->format(fn ($value, Promotion $row, Column $column) => $this->getPromotionStatus($row).' '.Str::limit($value, 50))
                ->html()
                ->collapseOnMobile(),
            Column::make(__('admin.Type'), 'type')
                ->sortable()
                ->format(
                    fn ($value, Promotion $row, Column $column) => __('admin.'.Promotion::getTypeName($row->type))
                ),
            Column::make(__('admin.Object'), 'type')
                ->format(fn ($value, Promotion $row, Column $column) => $this->getObjectName($value, $row, $column)),
            Column::make(__('admin.Value'), 'value')
                ->sortable()
                ->format(
                    fn ($value, Promotion $row, Column $column) => sprintf('%s %s', $value, $row->type_value == 1 ? 'PLN' : '%')
                ),
            Column::make(__('admin.Period'), 'id')
                ->format(
                    fn ($value, Promotion $row, Column $column) => (new DatePeriodDecorator)->decorate(Carbon::parse($row->start_at), Carbon::parse($row->end_at), 'm-d')
                ),
            Column::make(__('admin.Actions'), 'id')
                ->format(
                    function ($value, Promotion $row, Column $column) {
                        return view('components.admin.datatable.actions', [
                            'buttons' => [
                                $this->prepareEditButton($row->id, 'promotion', 'promotions'),
                                $this->prepareDeleteButton($row->id, 'promotion', 'promotions'),
                            ],
                        ]);
                    }
                )
                ->html(),
        ];
    }

    private function getObjectName($value, Promotion $row, Column $column)
    {
        $promotonType = PromotionType::from($value);
        if ($promotonType == PromotionType::DISH) {
            return $row->orderDish?->name ?? '-';
        } elseif ($promotonType == PromotionType::CATEGORY) {
            return $row->orderCategory?->name ?? '-';
        }

        return '-';
    }

    private function getPromotionStatus(Promotion $row): string
    {
        $now = Carbon::now()->startOfDay();
        $startAt = Carbon::parse($row->start_at)->startOfDay();
        $endAt = $row->end_at ? Carbon::parse($row->end_at)->endOfDay() : null;

        $isInDateRange = $startAt <= $now && ($endAt >= $now || is_null($endAt));
        $isActive = $row->active && $isInDateRange;
        return (new BoolStatusDecorator())->decorate($isActive);
    }
}
