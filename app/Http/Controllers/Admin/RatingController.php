<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RatingRequest;
use App\Http\Resources\Admin\RatingResource;
use App\Managers\RatingManager;
use App\Models\Rating;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\View\View;

class RatingController extends Controller
{
    /**
     * @var RatingManager
     */
    private $manager;

    public function __construct()
    {
        $this->manager = new RatingManager();
        RatingResource::wrap('results');
    }

    /**
     * @param Request $request
     *
     * @return View|Factory|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $order = $request->query->get('order', $request->session()->get('order_rating', ['id' => 'asc']));
        $filter = $request->query->get('filter', $request->session()->get('filter_rating'));
        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return RatingResource::collection(Rating::getPaginatedForPanel($request->get('query_rating'), RatingResource::LIMIT, $order, null));
        } else {
            $rows = RatingResource::collection(Rating::getPaginatedForPanel($request->get('query_rating', $request->session()->get('query_rating')), RatingResource::LIMIT, $order, $filter));
        }
        ! $request->has('query_rating') ?: $request->session()->put('query_rating', $request->get('query_rating'));
        ! $request->has('order') ?: $request->session()->put('order_rating', $request->get('order'));

        return view('admin.ratings.index')->with([
            'controller' => 'rating',
            'action' => 'index',
            'rows' => $rows,
            'order' => $order,
            'filter' => $filter,
        ]);
    }

    /**
     * @param Request $request
     * @param Rating $rating
     *
     * @return RatingResource|View|Factory
     */
    public function show(Request $request, Rating $rating)
    {
        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return new RatingResource($rating);
        }

        return view('admin.ratings.show')->with([
            'controller' => 'rating',
            'action' => 'show',
            'data' => new RatingResource($rating),
        ]);
    }

    /**
     * @return View|Factory
     */
    public function create()
    {
        $rating = new Rating;

        return view('admin.ratings.form')->with([
            'controller' => 'rating',
            'action' => 'create',
            'data' => new RatingResource($rating),
        ]);
    }

    /**
     * store function.
     *
     * @param RatingRequest $request
     *
     * @return RedirectResponse
     */
    public function store(RatingRequest $request)
    {
        $this->manager->create($request);

        $request->session()->flash('alert-success', __('admin.Rating was created'));

        return $this->redirectToIndex($request, 'admin.ratings.index');
    }

    /**
     * @param Rating $rating
     *
     * @return View|Factory
     */
    public function edit(Rating $rating)
    {
        return view('admin.ratings.form')->with([
            'controller' => 'rating',
            'action' => 'edit',
            'data' => new RatingResource($rating),
        ]);
    }

    /**
     * @param RatingRequest $request
     * @param Rating $rating
     *
     * @return RedirectResponse
     */
    public function update(RatingRequest $request, Rating $rating)
    {
        $this->manager->update($request, $rating);

        $request->session()->flash('alert-success', __('admin.Rating was updated'));

        return $this->redirectToIndex($request, 'admin.ratings.index');
    }

    /**
     * @param Request $request
     * @param Rating $rating
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Rating $rating)
    {
        $this->manager->delete($rating);

        $request->session()->flash('alert-success', __('admin.Rating was deleted'));

        return redirect()->route('admin.ratings.index');
    }
}
