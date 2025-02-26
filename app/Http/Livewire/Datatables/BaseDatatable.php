<?php

namespace App\Http\Livewire\Datatables;

use App\Models\DeliveryRange;
use App\Models\Report;
use App\Services\Datatable\ActionButton;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

abstract class BaseDatatable extends DataTableComponent
{
    const string RESTAURANT_ID_KEY = 'restaurant_id';
    public $showDeleteModal = false;
    public $deleteId = '';
    protected $createLink = '';
    protected $boldColumn = 'name';

    public function configure(): void
    {
        $this->dispatchBrowserEvent('rerenderScrollBar');
        $this->setPrimaryKey('id');
        $this->setDefaultAreas();
        $this->setDefaultStyles();
    }

    private function setDefaultAreas()
    {
        $this->setColumnSelectDisabled();
        $areas = [
            'before-tools' => ['admin.partials.table.delete-modal'],
        ];
        if ($this->createLink) {
            $areas['toolbar-right-end'] = [
                'admin.partials.table.extra-buttons', [
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
        }

        $this->setConfigurableAreas($areas);
    }

    private function setDefaultStyles()
    {
        $this->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {
            if (!$column->isField($this->boldColumn)) {
                return [
                    'default' => false,
                    'class' => 'px-6 py-4 whitespace-nowrap text-sm font-light text-gray-900 dark:text-gray-50',
                ];
            }

            return [];
        });
    }

    public function bulkDelete()
    {
        foreach ($this->getSelected() as $id) {
            $model = app($this->model)->findOrFail($id);
            $model->delete();
        }
        $this->selected = [];
        $this->emit('refreshDatatable');
    }

    public function delete(int $id)
    {
        $this->showDeleteModal = true;
        $this->deleteId = $id;
    }

    public function confirmDelete()
    {
        $model = app($this->model)->findOrFail($this->deleteId);
        Report::where(self::RESTAURANT_ID_KEY, $this->deleteId)->delete();
        $model->delete();

        $rows = $this->getRows();
        if ($rows instanceof \Illuminate\Contracts\Pagination\Paginator) {
            $currentPage = $rows->currentPage();
            $itemsDisplayedOnCurrentPage = $rows->count();
            if ($itemsDisplayedOnCurrentPage === 0 && $currentPage > 1) {
                $this->previousPage();
            }
        }

        $this->closeDeleteModal();

        if ($model instanceof DeliveryRange) {
            $this->dispatchBrowserEvent('deleteDeliveryRange');
        }

        $this->emit('refreshDatatable');
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteId = '';
    }

    public function prepareDuplicateButton(int $id, string $elementName, string $routeName): ActionButton
    {
        return new ActionButton(
            route('admin.' . $routeName . '.duplicate', [$elementName => $id]),
            __('Duplicate'),
            'duplicate',
            $id
        );
    }

    public function prepareEditButton(int $id, string $elementName, string $routeName): ActionButton
    {
        return new ActionButton(
            route('admin.' . $routeName . '.edit', [$elementName => $id]),
            __('admin.Edit'),
            'edit',
            $id
        );
    }

    public function prepareShowButton(int $id, string $elementName, string $routeName): ActionButton
    {
        return new ActionButton(
            route('admin.' . $routeName . '.show', [$elementName => $id]),
            __('admin.Show'),
            'show',
            $id
        );
    }

    public function prepareDeleteButton(int $id, string $elementName, string $routeName): ActionButton
    {
        return new ActionButton(
            route('admin.' . $routeName . '.delete', [$elementName => $id]),
            __('admin.Delete'),
            'delete',
            $id
        );
    }
}
