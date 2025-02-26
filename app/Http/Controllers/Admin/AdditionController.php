<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Select2Trait;
use App\Http\Requests\Admin\AdditionRequest;
use App\Http\Resources\Admin\AdditionGroupResource;
use App\Http\Resources\Admin\AdditionResource;
use App\Managers\AdditionManager;
use App\Models\Addition;
use App\Models\AdditionGroup;
use App\Services\TranslationService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\View\View;

class AdditionController extends Controller
{
    use Select2Trait;

    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var AdditionManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        $this->transService = $service;
        $this->manager = new AdditionManager($this->transService);
        AdditionResource::wrap('results');
    }

    /**
     * @param Request $request
     *
     * @return View|Factory|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        return view('admin.additions.index')->with([
            'controller' => 'addition',
            'action' => 'index',
        ]);
    }

    /**
     * @param Request $request
     * @param Addition $addition
     *
     * @return AdditionResource|View|Factory
     */
    public function show(Request $request, Addition $addition)
    {
        return view('admin.additions.show')->with([
            'controller' => 'addition',
            'action' => 'show',
            'data' => new AdditionResource($addition),
        ]);
    }

    /**
     * @return View|Factory
     */
    public function create(Request $request)
    {
        return view('admin.additions.form')->with($this->hydrateData([
            'controller' => 'addition',
            'action' => 'create',
            'data' => new AdditionResource(new Addition),
            'oldAdditionGroups' => $this->getOldArrayForSelect2('addition_addition_group', 'id'),
            'defaultRedirectUrl' => route('admin.additions.index'),
        ], $request));
    }

    /**
     * store function.
     *
     * @param AdditionRequest $request
     *
     * @return RedirectResponse
     */
    public function store(AdditionRequest $request)
    {
        $this->manager->createFromRequest($request);

        $request->session()->flash('alert-success', __('admin.Addition was created'));

        return $this->redirectToIndex($request, 'admin.additions.index');
    }

    /**
     * @param Addition $addition
     *
     * @return View|Factory
     */
    public function edit(Request $request, Addition $addition)
    {
        return view('admin.additions.form')->with($this->hydrateData([
            'controller' => 'addition',
            'action' => 'edit',
            'data' => new AdditionResource($addition),
            'oldAdditionGroups' => $this->getOldArrayForSelect2('addition_addition_group', 'id'),
            'defaultRedirectUrl' => route('admin.additions.index'),
        ], $request));
    }

    /**
     * @param AdditionRequest $request
     * @param Addition $addition
     *
     * @return RedirectResponse
     */
    public function update(AdditionRequest $request, Addition $addition)
    {
        $this->manager->updateFromRequest($request, $addition);

        $request->session()->flash('alert-success', __('admin.Addition was updated'));

        return $this->redirectToIndex($request, 'admin.additions.index');
    }

    /**
     * @param Request $request
     * @param Addition $addition
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Addition $addition)
    {
        $this->manager->delete($addition);

        $request->session()->flash('alert-success', __('admin.Addition was deleted'));

        return redirect()->route('admin.additions.index');
    }

    public function addition_groups(?int $id = null)
    {
        $additionResource = new AdditionResource($id ? Addition::findOrFail($id) : new Addition);
        $additionGroups = AdditionGroupResource::collection(AdditionGroup::all());
        $response = $this->getJsonStringForSelect2($additionGroups, $additionResource->additions_additions_groups, 'addition_group_id');

        return $response;
    }
}
