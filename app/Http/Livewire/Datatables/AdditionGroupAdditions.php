<?php

namespace App\Http\Livewire\Datatables;

use App\Decorators\MoneyDecorator;
use App\Models\Addition;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class AdditionGroupAdditions extends BaseDatatable
{
    protected $model = Addition::class;

    public $additionGroupId;

    public int $perPage = 100;

    //create new addition fields
    public $additionName = '';

    public $additionPrice = 0;

    public function configure(): void
    {
        $this->createLink = false;
        parent::configure();

        $this->setColumnSelectDisabled();
        $this->setPaginationDisabled();
        $this->setupPagination();

        $this->dispatchBrowserEvent('rerenderNewInputs');
//        $this->setSearchDisabled();

        $areas = $this->getConfigurableAreas();
        $areas['toolbar-right-end'] = [
            'admin.additions_groups.partials.additions-table-actions', [
                'buttons' => [
                    [
                        'link' => $this->createLink,
                        'type' => 'link',
                        'color' => 'success',
                        'label' => __('admin.Create'),
                    ],
                ],
            ],
        ];

        $this->setConfigurableAreas($areas);
    }

    public function builder(): Builder
    {
        return Addition::query()
            ->whereHas('additions_additions_groups', function (Builder $query) {
                $query->where('addition_group_id', $this->additionGroupId);
            });
    }

    public function columns(): array
    {
        return [
            Column::make(__('admin.Name'), 'name')
                ->sortable()
                ->searchable(function ($query, $searchTerm) {
                    $query->whereRaw('LOWER(additions.name) LIKE ?', ["%".strtolower($searchTerm)."%"]);
                }),
            Column::make(__('admin.Price'), 'price')
                ->sortable()
                ->format(
                    fn ($value, Addition $row, Column $column) => (new MoneyDecorator())->decorate($value, 'PLN')
                ),
            Column::make(__('admin.Actions'), 'id')
                ->format(
                    function ($value, Addition $row, Column $column) {
                        return view('components.admin.datatable.actions', [
                            'buttons' => [
                                $this->prepareEditButton($row->id, 'addition', 'additions'),
                                $this->prepareDeleteButton($row->id, 'addition', 'additions'),
                            ],
                        ]);
                    }
                )
                ->html(),

        ];
    }

    public function createAddition()
    {
        $addition = new Addition([
            'name' => $this->additionName,
            'price' => $this->additionPrice,
        ]);
        $addition->save();
        $addition->additions_additions_groups()->create(['addition_group_id' => $this->additionGroupId]);

        $this->reset(['additionName', 'additionPrice']);
        $this->emit('refreshDatatable');
    }
}
