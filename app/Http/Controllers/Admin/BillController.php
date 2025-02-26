<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BillRequest;
use App\Http\Resources\Admin\BillResource;
use App\Managers\BillManager;
use App\Models\Bill;
use App\Models\Restaurant;
use App\Models\User;
use App\Services\TranslationService;
use Hyn\Tenancy\Facades\TenancyFacade;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BillController extends Controller
{
    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var BillManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        $this->transService = $service;
        $this->manager = new BillManager($this->transService);
        BillResource::wrap('results');
    }

    /**
     * @param Request $request
     *
     * @return View|Factory|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $cancel_bill = $user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN]) ? true : false;

        return view('admin.bills.index')->with([
            'controller' => 'bill',
            'action' => 'index',
            'ajax' => false,
        ]);
    }

    /**
     * @param Request $request
     * @param Bill $order
     *
     * @return BillResource|View|Factory
     */
    public function show(Request $request, Bill $bill)
    {
        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return new BillResource($bill);
        }

        if ($website = TenancyFacade::website()) {
            $restaurant = Restaurant::where('hostname', $website->uuid)->first();
        }

        return view('admin.bills.show')->with($this->hydrateData([
            'controller' => 'bill',
            'action' => 'show',
            'data' => new BillResource($bill),
            'cancel_order' => Auth::user()->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN]),
            'res_id' => isset($restaurant) ? $restaurant->id : null,
            'defaultRedirectUrl' => route('admin.bills.index'),
        ], $request));
    }

    /**
     * @return View|Factory
     */
    public function create()
    {
        $bill = new Bill;

        return view('admin.bills.form')->with([
            'controller' => 'bill',
            'action' => 'create',
            'data' => new BillResource($bill),
        ]);
    }

    /**
     * store function.
     *
     * @param BillRequest $request
     *
     * @return RedirectResponse
     */
    public function store(BillRequest $request)
    {
        $this->manager->createFromRequest($request);

        $request->session()->flash('alert-success', __('admin.Bill was created'));

        return $this->redirectToIndex($request, 'admin.bills.index');
    }

    /**
     * @param Bill $order
     *
     * @return View|Factory
     */
    public function edit(Bill $bill)
    {
        return view('admin.bills.form')->with([
            'controller' => 'bill',
            'action' => 'edit',
            'data' => new BillResource($bill),
        ]);
    }

    /**
     * @param BillRequest $request
     * @param Bill $order
     *
     * @return RedirectResponse
     */
    public function update(BillRequest $request, Bill $bill)
    {
        $this->manager->update($request, $bill);

        $request->session()->flash('alert-success', __('admin.Bill was updated'));

        return $this->redirectToIndex($request, 'admin.bills.index');
    }

    /**
     * @param Request $request
     * @param Bill $order
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Bill $bill)
    {
        $this->manager->delete($bill);

        $request->session()->flash('alert-success', __('admin.Bill was deleted'));

        return redirect()->route('admin.bills.index');
    }

    public function status_edit(Request $request)
    {
        $user = Auth::user();

        if ($request->get('value') == 4 && !$user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN])) {
            return ['status' => 'nope'];
        }

        $this->manager->status_edit($request);

        return ['status' => 'ok'];
    }

    public function paid_edit(Request $request)
    {
        $user = Auth::user();

        if (!$user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN, User::ROLE_WAITER])) {
            return ['status' => 'nope'];
        }

        $this->manager->paid_edit($request);

        return ['status' => 'ok'];
    }

    public function time_wait_edit(Request $request)
    {
        $user = Auth::user();

        if ($request->get('value') == 4 && !$user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN])) {
            return ['status' => 'nope'];
        }

        $this->manager->time_wait_edit($request);

        return ['status' => 'ok'];
    }

    public function modal_table(Request $request)
    {
        $website = TenancyFacade::website();
        $restaurant_id = $request->get('restaurant_id');
        if ($restaurant_id && !$website) {
            $environment = app(\Hyn\Tenancy\Environment::class);
            $restaurant = Restaurant::where('id', $restaurant_id)->first();
            $website = \Hyn\Tenancy\Models\Website::where('uuid', $restaurant->hostname)->first();
            $hostname = \Hyn\Tenancy\Models\Hostname::where('website_id', $website->id)->first();
            $environment->tenant($website);
        }

        $createdAt = $request->get('createdAt');

        $rows = BillResource::collection(Bill::getPaginatedForStatistics($createdAt));

        $data = view('admin.bills.partials.table_modal')->with([
            'controller' => 'bill',
            'action' => 'index',
            'rows' => $rows,
            'ajax' => false,
            'restaurant_id' => $restaurant_id,
        ])->render();

        return ['status' => 200, 'data' => $data];
    }

    /**
     * accept bill function.
     *
     * @param Bill $bill
     *
     * @return RedirectResponse
     */
    public function accept(Request $request, Bill $bill)
    {
        if (!$bill->canBeAccepted()) {
            $request->session()->flash('alert-danger', __('admin.Order cannot be accepted'));

            return redirect()->route('admin.bills.show', ['bill' => $bill]);
        }

        $this->manager->accept($bill);

        $request->session()->flash('alert-success', __('admin.Order is accepted'));

        return redirect()->route('admin.bills.show', ['bill' => $bill]);
    }

    /**
     * refund payment of the bill function.
     *
     * @param BillRequest $request
     * @param Bill $bill
     *
     * @return RedirectResponse
     */
    public function refund(BillRequest $request, Bill $bill)
    {
        if (!$bill->isRefund()) {
            $request->session()->flash('alert-danger', __('admin.Payment cannot be refunded'));

            return redirect()->route('admin.bills.show', ['bill' => $bill])->withInput();
        }

        $amount = $this->manager->getRefundAmount($request, $bill);
        if ($amount > ($bill->payment->p24_amount / 100) || $amount < 1) {
            $request->session()->flash('alert-danger', __('admin.Payment cannot be refunded, refund exceed the payment amount'));

            return redirect()->route('admin.bills.show', ['bill' => $bill])->withInput();
        }

        $this->manager->refund($request, $bill, $amount);

        $request->session()->flash('alert-success', __('admin.Payment is ready to refund'));

        return redirect()->route('admin.bills.show', ['bill' => $bill]);
    }

    /**
     * accept bill function.
     *
     * @param Bill $bill
     *
     * @return RedirectResponse
     */
    public function ready(Request $request, Bill $bill)
    {
        if (!$bill->canBeReady()) {
            $request->session()->flash('alert-danger', __('admin.Order cannot be ready'));

            return redirect()->route('admin.bills.show', ['bill' => $bill]);
        }

        $this->manager->ready($bill);

        $request->session()->flash('alert-success', __('admin.Order is ready'));

        return redirect()->route('admin.bills.show', ['bill' => $bill]);
    }
}
