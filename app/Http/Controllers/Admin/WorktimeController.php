<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WorktimeRequest;
use App\Http\Resources\Admin\WorktimeResource;
use App\Managers\WorktimeManager;
use App\Models\Worktime;
use App\Services\TranslationService;
use App\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WorktimeController extends Controller
{
    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var WorktimeManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        $this->transService = $service;
        $this->manager = new WorktimeManager($this->transService);
        WorktimeResource::wrap('results');
    }

    /**
     * @param Request $request
     *
     * @return View|Factory|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        return view('admin.worktimes.index')->with([
            'controller' => 'worktime',
            'action' => 'index',
        ]);
    }

    /**
     * @param Request $request
     * @param Worktime $worktime
     *
     * @return WorktimeResource|View|Factory
     */
    public function show(Request $request, Worktime $worktime)
    {
        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return new WorktimeResource($worktime);
        }
        $user = Auth::user();
        $cancel_order = $user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN]) ? true : false;

        return view('admin.worktimes.show')->with([
            'controller' => 'worktime',
            'action' => 'show',
            'data' => new WorktimeResource($worktime),
            'cancel_order' => $cancel_order,
        ]);
    }

    /**
     * @return View|Factory
     */
    public function create(Request $request)
    {
        $worktime = new Worktime;

        return view('admin.worktimes.form')->with($this->hydrateData([
            'controller' => 'worktime',
            'action' => 'create',
            'data' => new WorktimeResource($worktime),
            'defaultRedirectUrl' => route('admin.worktimes.index'),
        ], $request));
    }

    /**
     * store function.
     *
     * @param WorktimeRequest $request
     *
     * @return RedirectResponse
     */
    public function store(WorktimeRequest $request)
    {
        $this->manager->create($request);

        $request->session()->flash('alert-success', __('admin.Worktime was created'));

        return $this->redirectToIndex($request, 'admin.worktimes.index');
    }

    /**
     * @param Worktime $worktime
     *
     * @return View|Factory
     */
    public function edit(Request $request, Worktime $worktime)
    {
        return view('admin.worktimes.form')->with($this->hydrateData([
            'controller' => 'worktime',
            'action' => 'edit',
            'data' => new WorktimeResource($worktime),
            'defaultRedirectUrl' => route('admin.worktimes.index'),
        ], $request));
    }

    /**
     * @param WorktimeRequest $request
     * @param Worktime $order
     *
     * @return RedirectResponse
     */
    public function update(WorktimeRequest $request, Worktime $worktime)
    {
        $this->manager->update($request, $worktime);

        $request->session()->flash('alert-success', __('admin.Worktime was updated'));

        return $this->redirectToIndex($request, 'admin.worktimes.index');
    }

    /**
     * @param Request $request
     * @param Worktime $worktime
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Worktime $worktime)
    {
        $this->manager->delete($worktime);

        $request->session()->flash('alert-success', __('admin.Worktime was deleted'));

        return redirect()->route('admin.worktimes.index');
    }
}
