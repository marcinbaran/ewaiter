<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderRequest;
use App\Http\Resources\Admin\OrderResource;
use App\Managers\OrderManager;
use App\Models\User;
use App\Order;
use App\Services\TicketService;
use App\Services\TranslationService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var OrderManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        $this->transService = $service;
        $this->manager = new OrderManager($this->transService);
        OrderResource::wrap('results');
    }

    /**
     * @param Request $request
     *
     * @return View|Factory|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $cancel_order = $user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN]) ? true : false;

        return view('admin.orders.index')->with([
            'controller' => 'order',
            'action' => 'index',
        ]);
    }

    /**
     * @param Request $request
     * @param Order $order
     *
     * @return OrderResource|View|Factory
     */
    public function show(Request $request, Order $order)
    {
        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return new OrderResource($order);
        }

        return view('admin.orders.show')->with([
            'controller' => 'order',
            'action' => 'show',
            'data' => new OrderResource($order),
        ]);
    }

    /**
     * @return View|Factory
     */
    public function create()
    {
        $order = new Order;

        return view('admin.orders.form')->with([
            'controller' => 'order',
            'action' => 'create',
            'data' => new OrderResource($order),
        ]);
    }

    /**
     * store function.
     *
     * @param OrderRequest $request
     *
     * @return RedirectResponse
     */
    public function store(OrderRequest $request)
    {
        $this->manager->createFromRequest($request);

        $request->session()->flash('alert-success', __('admin.Order was created'));

        return $this->redirectToIndex($request, 'admin.orders.index');
    }

    /**
     * @param Order $order
     *
     * @return View|Factory
     */
    public function edit(Order $order)
    {
        return view('admin.orders.form')->with([
            'controller' => 'order',
            'action' => 'edit',
            'data' => new OrderResource($order),
        ]);
    }

    /**
     * @param OrderRequest $request
     * @param Order $order
     *
     * @return RedirectResponse
     */
    public function update(OrderRequest $request, Order $order)
    {
        $this->manager->update($request, $order);

        $request->session()->flash('alert-success', __('admin.Order was updated'));

        return $this->redirectToIndex($request, 'admin.orders.index');
    }

    /**
     * @param Request $request
     * @param Order $order
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Order $order)
    {
        $this->manager->delete($order);

        $request->session()->flash('alert-success', __('admin.Order was deleted'));

        return redirect()->route('admin.orders.index');
    }

    public function status_edit(Request $request)
    {
        $user = Auth::user();

        if ($request->get('value') == 4 && ! $user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN])) {
            return ['status' => 'nope'];
        }

        $this->manager->status_edit($request);

        return ['status' => 'ok'];
    }

    public function modal_table(Request $request)
    {
        $order = $request->query->get('order', $request->session()->get('order_order', ['id' => 'desc']));
        $filter = $request->query->get('filter', $request->session()->get('filter_order'));
        $filter['bill_id'] = $request->get('bill_id');

        $rows = OrderResource::collection(Order::getPaginatedForPanel($request->get('query_order', $request->session()->get('query_order')), OrderResource::LIMIT, $order, $filter));

        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return $rows;
        }
        ! $request->has('query_order') ?: $request->session()->put('query_order', $request->get('query_order'));
        ! $request->has('order') ?: $request->session()->put('order_order', $request->get('order'));
        ! $request->has('filter') ?: $request->session()->put('filter_order', $request->get('filter'));

        $user = Auth::user();
        $cancel_order = $user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN]) ? true : false;

        return view('admin.orders.partials.table_modal')->with([
            'controller' => 'order',
            'action' => 'index',
            'rows' => $rows,
            'order' => $order,
            'filter' => $filter,
            'cancel_order' => $cancel_order,
        ])->render();
    }

    public function modal_table_stats(Request $request)
    {
        $website = \Hyn\Tenancy\Facades\TenancyFacade::website();
        $restaurant_id = $request->get('restaurant_id');
        if ($restaurant_id && ! $website) {
            $environment = app(\Hyn\Tenancy\Environment::class);
            $restaurant = \App\Models\Restaurant::where('id', $restaurant_id)->first();
            $website = \Hyn\Tenancy\Models\Website::where('uuid', $restaurant->hostname)->first();
            $hostname = \Hyn\Tenancy\Models\Hostname::where('website_id', $website->id)->first();
            $environment->tenant($website);
        }

        $order = $request->query->get('order', $request->session()->get('order_order', ['id' => 'desc']));
        $filter = $request->query->get('filter', $request->session()->get('filter_order'));
        $filter['bill_id'] = $request->get('bill_id');

        $rows = OrderResource::collection(Order::getPaginatedForPanel($request->get('query_order', $request->session()->get('query_order')), OrderResource::LIMIT, $order, $filter));

        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return $rows;
        }
        ! $request->has('query_order') ?: $request->session()->put('query_order', $request->get('query_order'));
        ! $request->has('order') ?: $request->session()->put('order_order', $request->get('order'));
        ! $request->has('filter') ?: $request->session()->put('filter_order', $request->get('filter'));

        $user = Auth::user();
        $cancel_order = $user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN]) ? true : false;

        return view('admin.orders.partials.table_modal_stats')->with([
            'controller' => 'order',
            'action' => 'index',
            'rows' => $rows,
            'order' => $order,
            'filter' => $filter,
            'cancel_order' => $cancel_order,
        ])->render();
    }

    public function ticket(Request $request)
    {
        $ticketService = new TicketService();
        $ticketService->method('/api/DaneRachunku');
        $ticketService->data(
            [
                'id' => 2,
                'nrZamowienienia' => 'PS/22/2020',
                'data' => '2020-06-22',
                'rodzaj' => 'Do Stolika',
                'pozycjeZamowienia' => [
                    0 => [
                        'id' => 1,
                        'nazwa' => 'gg22',
                        'ilosc' => 1,
                        'uwagi' => 'erw22',
                    ],
                ],
                'uwagi' => 'tes33t',
            ]
        );
        dd($ticketService->send());
    }
}
