<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DeliveryRangeRequest;
use App\Http\Resources\Admin\DeliveryRangeResource;
use App\Managers\DeliveryRangeManager;
use App\Models\DeliveryRange;
use App\Models\Restaurant;
use App\Models\Settings;
use App\Services\GeoServices\GeoService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\View\View;

class DeliveryRangeController extends Controller
{
    /**
     * @var DeliveryRangeManager
     */
    private $manager;
    private readonly GeoService $geoService;

    public function __construct()
    {
        $this->manager = new DeliveryRangeManager();
        $this->geoService = app(GeoService::class);
        DeliveryRangeResource::wrap('results');
    }

    /**
     * @param Request $request
     *
     * @return View|Factory|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $delivery_settings_key = Settings::where('key', config('settings.delivery_range_key'))->pluck('id')->first();
        $order = $request->query->get('order', $request->session()->get('order_delivery_range', ['id' => 'asc']));
        $filter = $request->query->get('filter', $request->session()->get('filter_delivery_range'));
        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return DeliveryRangeResource::collection(DeliveryRange::getPaginatedForPanel($request->get('query_delivery_range'), DeliveryRangeResource::LIMIT, $order, null));
        } else {
            $rows = DeliveryRangeResource::collection(DeliveryRange::getPaginatedForPanel($request->get('query_delivery_range', $request->session()->get('query_delivery_range')), DeliveryRangeResource::LIMIT, $order, $filter));
        }
        !$request->has('query_delivery_range') ?: $request->session()->put('query_delivery_range', $request->get('query_delivery_range'));
        !$request->has('order') ?: $request->session()->put('order_delivery_range', $request->get('order'));

        $tables = \App\Table::orderBy('number')->get();

        return view('admin.delivery_ranges.index')->with([
            'controller' => 'delivery_range',
            'action' => 'index',
            'rows' => $rows,
            'order' => $order,
            'tables' => $tables,
            'filter' => $filter,
            'delivery_settings_key' => $delivery_settings_key,
        ]);
    }

    public function getDeliveryRangesCoordinatesArray(Request $request)
    {
        try {
            $delivery_ranges = DeliveryRange::all();
            $coordinates = $delivery_ranges->pluck('range_polygon');
            $restaurant = Restaurant::getCurrentRestaurant();
            $restaurantCords = $this->geoService->getCoordsForRestaurant($restaurant);

            $restaurantAddress = $restaurant->getFormattedAddress();


            return response()->json([
                "coordinates" => $coordinates,
                "restaurantCords" => $restaurantCords->toArray(),
                "address" => $restaurantAddress,
                "restaurantName" => $restaurant->name
            ]);
        } catch (\Exception $err) {
            return response()->json([
                'message' => $err->getMessage()
            ], 500);
        }
    }

    /**
     * @param Request $request
     * @param DeliveryRange $delivery_range
     *
     * @return DeliveryRangeResource|View|Factory
     */
    public function show(Request $request, DeliveryRange $delivery_range)
    {
        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return new DeliveryRangeResource($delivery_range);
        }
        $delivery_settings_key = Settings::where('key', config('settings.delivery_range_key'))->pluck('id')->first();

        return view('admin.delivery_ranges.show')->with([
            'controller' => 'delivery_range',
            'action' => 'show',
            'data' => new DeliveryRangeResource($delivery_range),
            'delivery_settings_key' => $delivery_settings_key,
        ]);
    }

    /**
     * store function.
     *
     * @param DeliveryRangeRequest $request
     *
     * @return RedirectResponse
     */
    public function store(DeliveryRangeRequest $request)
    {
        $this->manager->create($request);
        $delivery_settings_key = Settings::where('key', config('settings.delivery_range_key'))->pluck('id')->first();

        $request->session()->flash('alert-success', __('admin.DeliveryRange was created'));

        return $this->redirectToIndex($request, 'admin.settings.edit', ['settings' => $delivery_settings_key]);
    }

    /**
     * @return View|Factory
     */
    public function create(Request $request)
    {
        $delivery_range = new DeliveryRange;
        $delivery_settings_key = Settings::where('key', config('settings.delivery_range_key'))->pluck('id')->first();

        if (DeliveryRange::get()->where('out_of_range', 1)->count() > 0) {
            $request->session()->flash('alert-danger', __('validation.delivery_range.out_of_range_exists'));

            return redirect()->route('admin.settings.edit', ['settings' => $delivery_settings_key]);
        }

        return view('admin.delivery_ranges.form')->with($this->hydrateData([
            'controller' => 'delivery_range',
            'action' => 'create',
            'data' => new DeliveryRangeResource($delivery_range),
            'delivery_settings_key' => $delivery_settings_key,
            'defaultRedirectUrl' => route('admin.settings.edit', ['settings' => $delivery_settings_key]),
        ], $request));
    }

    /**
     * @param DeliveryRange $delivery_range
     *
     * @return View|Factory
     */
    public function edit(Request $request, DeliveryRange $delivery_range)
    {
        $delivery_settings_key = Settings::where('key', config('settings.delivery_range_key'))->pluck('id')->first();

        return view('admin.delivery_ranges.form')->with($this->hydrateData([
            'controller' => 'delivery_range',
            'action' => 'edit',
            'data' => new DeliveryRangeResource($delivery_range),
            'delivery_settings_key' => $delivery_settings_key,
            'defaultRedirectUrl' => route('admin.settings.edit', ['settings' => $delivery_settings_key]),
        ], $request));
    }

    /**
     * @param DeliveryRangeRequest $request
     * @param DeliveryRange $delivery_range
     *
     * @return RedirectResponse
     */
    public function update(DeliveryRangeRequest $request, DeliveryRange $delivery_range)
    {
        $delivery_settings_key = Settings::where('key', config('settings.delivery_range_key'))->pluck('id')->first();
        $this->manager->update($request, $delivery_range);

        $request->session()->flash('alert-success', __('admin.DeliveryRange was updated'));

        return $this->redirectToIndex($request, 'admin.settings.edit', ['settings' => $delivery_settings_key]);
    }

    /**
     * @param Request $request
     * @param DeliveryRange $delivery_range
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, DeliveryRange $delivery_range)
    {
        $delivery_settings_key = Settings::where('key', config('settings.delivery_range_key'))->pluck('id')->first();
        $this->manager->delete($delivery_range);

        $request->session()->flash('alert-success', __('admin.DeliveryRange was deleted'));

        return redirect()->route('admin.settings.edit', ['settings' => $delivery_settings_key]);
    }
}
