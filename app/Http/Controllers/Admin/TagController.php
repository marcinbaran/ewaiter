<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TagRequest;
use App\Http\Resources\Admin\TagResource;
use App\Managers\TagManager;
use App\Models\Tag;
use App\Services\TranslationService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TagController extends Controller
{
    public $user_roles;
    /**
     * @var TranslationService
     */
    private $transService;
    /**
     * @var TagManager
     */
    private $manager;

    public function __construct(TranslationService $service, public array $options = [])
    {
        $this->user_roles = Auth::user()->roles;
        $this->transService = $service;
        $this->manager = new TagManager($this->transService);
        TagResource::wrap('results');
    }

    /**
     * @param Request $request
     *
     * @return View|Factory|AnonymousResourceCollection
     */
    public function index()
    {
        return view('admin.tags.index')->with([
            'controller' => 'tag',
            'action' => 'index',
        ]);
    }

    /**
     * @param Request $request
     * @param Tag $tag
     *
     * @return TagResource|View|Factory
     */
    public function show(Request $request, Tag $tag)
    {
        return view('admin.tags.show')->with([
            'controller' => 'tag',
            'action' => 'show',
            'data' => new TagResource($tag),
        ]);
    }

    /**
     * store function.
     *
     * @param TagRequest $request
     *
     * @return RedirectResponse
     */
    public function store(TagRequest $request)
    {
        $this->manager->create($request);

        $request->session()->flash('alert-success', __('admin.Tag was created'));

        return $this->redirectToIndex($request, 'admin.tags.index');
    }

    /**
     * @return View|Factory
     */
    public function create(Request $request)
    {
        $tag = new Tag;

        return view('admin.tags.form')->with($this->hydrateData([
            'controller' => 'tag',
            'action' => 'create',
            'data' => new TagResource($tag),
            'defaultRedirectUrl' => route('admin.tags.index'),
            'user_roles' => $this->user_roles
        ], $request));
    }

    /**
     * @param Tag $tag
     *
     * @return View|Factory
     */
    public function edit(Request $request, Tag $tag)
    {
        return view('admin.tags.form')->with($this->hydrateData([
            'controller' => 'tag',
            'action' => 'edit',
            'data' => new TagResource($tag),
            'defaultRedirectUrl' => route('admin.tags.index'),
            'user_roles' => $this->user_roles
        ], $request));
    }

    /**
     * @param TagRequest $request
     * @param Tag $tag
     *
     * @return RedirectResponse
     */
    public function update(TagRequest $request, Tag $tag)
    {
        $this->manager->update($request, $tag);

        $request->session()->flash('alert-success', __('admin.Tag was updated'));

        return $this->redirectToIndex($request, 'admin.tags.index');
    }

    /**
     * @param Request $request
     * @param Tag $tag
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Tag $tag)
    {
        $this->manager->delete($tag);

        $request->session()->flash('alert-success', __('admin.Tag was deleted'));

        return redirect()->route('admin.tags.index');
    }
}
