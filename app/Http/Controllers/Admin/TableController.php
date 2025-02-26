<?php

namespace App\Http\Controllers\Admin;

use App\Enum\Table\TableCreateFormType;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Select2Trait;
use App\Http\Requests\Admin\TableRequest;
use App\Http\Resources\Admin\TableResource;
use App\Managers\TableManager;
use App\Models\Table;
use App\Services\TranslationService;
use Illuminate\Http\Request;

class TableController extends Controller
{
    use Select2Trait;

    private $transService;

    private $manager;

    public function __construct(TranslationService $service)
    {
        $this->transService = $service;
        $this->manager = new TableManager($this->transService);
        TableResource::wrap('results');
    }

    public function index(Request $request, Table $table)
    {
        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return TableResource::collection(Table::getPaginatedForPanel($request->get('query_table'), TableResource::LIMIT, ['name' => 'asc']));
        }

        return view('admin.tables.index')->with([
            'controller' => 'table',
            'action' => 'index',
            'data' => new TableResource($table),
        ]);
    }

    public function show(Request $request, Table $table)
    {
        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return new TableResource($table);
        }

        return view('admin.tables.show')->with([
            'controller' => 'table',
            'action' => 'show',
            'data' => new TableResource($table),
        ]);
    }

    public function create(Request $request)
    {
        $table = new Table;

        return view('admin.tables.form')->with($this->hydrateData([
            'controller' => 'table',
            'action' => 'create',
            'data' => new TableResource($table),
            'defaultRedirectUrl' => route('admin.tables.index'),
        ], $request));
    }

    public function store(TableRequest $request)
    {
        $this->manager->create($request);

        $request->session()->flash('alert-success', __('admin.Table was created'));

        return $this->redirectToIndex($request, 'admin.tables.index');
    }

    public function edit(Request $request, Table $table)
    {
        return view('admin.tables.form')->with($this->hydrateData([
            'controller' => 'table',
            'action' => 'edit',
            'data' => new TableResource($table),
            'defaultRedirectUrl' => route('admin.tables.index'),
        ], $request));
    }

    public function update(TableRequest $request, Table $table)
    {
        $isTableNumberChanged = $this->manager->isTableNumberChanged($request, $table);

        $this->manager->update($request, $table, true);

        $request->session()->flash('alert-success', $isTableNumberChanged ? __('admin.tables.table_data_changed_you_need_to_regenerate_qr_code') : __('admin.Table was updated'));

        return $this->redirectToIndex($request, 'admin.tables.index');
    }

    public function delete(Request $request, Table $table)
    {
        $this->manager->delete($table);

        $request->session()->flash('alert-success', __('admin.Table was deleted'));

        return redirect()->route('admin.tables.index');
    }

    public function createFormTypes()
    {
        $createFormTypes = collect(json_decode(json_encode($this->transformEnumKeyValuePairsForSelect2(TableCreateFormType::getKeyValuePairs(), 'table.create_form_type'))));

        return $this->getJsonStringForSelect2($createFormTypes, collect(), 'id');
    }
}
