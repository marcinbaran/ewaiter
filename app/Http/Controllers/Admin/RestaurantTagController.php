<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RestaurantTagRequest;
use App\Http\Resources\Admin\RestaurantTagResource;
use App\Managers\RestaurantTagManager;
use App\Models\RestaurantTag;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;

class RestaurantTagController extends Controller
{
    /**
     * @var RestaurantTagManager
     */
    private $manager;

    public function __construct()
    {
        $this->manager = new RestaurantTagManager();
        RestaurantTagResource::wrap('results');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|JsonResponse
     */
    public function index(Request $request)
    {
        return view('admin.restaurant_tags.index')->with([
            'controller' => 'restaurant',
            'action' => 'index',
        ]);
    }

    /**
     * @return View|Factory
     */
    public function create(Request $request)
    {
        $tag = new RestaurantTag;

        return view('admin.restaurant_tags.form')->with($this->hydrateData([
            'controller' => 'settings',
            'action' => 'create',
            'data' => new RestaurantTagResource($tag),
            'defaultRedirectUrl' => route('admin.restaurant_tags.index'),
        ], $request));
    }

    /**
     * store function.
     *
     * @param RestaurantTagRequest $request
     *
     * @return RedirectResponse
     */
    public function store(RestaurantTagRequest $request)
    {
        $this->manager->createFromRequest($request);

        $request->session()->flash('alert-success', __('admin.Tag created successfully'));

        return $this->redirectToIndex($request, 'admin.restaurant_tags.index');
    }

    /**
     * @param RestaurantTag $restaurant_tag
     *
     * @return View|Factory
     */
    public function edit(Request $request, RestaurantTag $restaurant_tag)
    {
        return view('admin.restaurant_tags.form')->with($this->hydrateData([
            'controller' => 'order',
            'action' => 'edit',
            'data' => new RestaurantTagResource($restaurant_tag),
            'defaultRedirectUrl' => route('admin.restaurant_tags.index'),
        ], $request));
    }

    /**
     * @param RestaurantRequest $request
     * @param RestaurantTag $restaurant_tag
     *
     * @return RedirectResponse
     */
    public function update(RestaurantTagRequest $request, RestaurantTag $restaurant_tag)
    {
        $this->manager->update($request, $restaurant_tag);

        $request->session()->flash('alert-success', __('admin.Tag was updated'));

        return $this->redirectToIndex($request, 'admin.restaurant_tags.index');
    }

    /**
     * @param Request $request
     * @param RestaurantTag $restaurant_tag
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, RestaurantTag $restaurant_tag)
    {
        $this->manager->delete($restaurant_tag);

        $request->session()->flash('alert-success', __('admin.Tag was deleted'));

        return response()->noContent();
    }
}
