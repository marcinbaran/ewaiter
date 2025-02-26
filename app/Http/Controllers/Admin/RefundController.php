<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RefundRequest;
use App\Http\Resources\Admin\BillResource;
use App\Http\Resources\Admin\PaymentResource;
use App\Http\Resources\Admin\RefundResource;
use App\Managers\RefundManager;
use App\Models\Refund;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\View\View;

class RefundController extends Controller
{
    /**
     * @var RefundManager
     */
    private $manager;

    public function __construct()
    {
        $this->manager = new RefundManager();
        RefundResource::wrap('results');
    }

    /**
     * @param Request $request
     *
     * @return View|Factory|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        return view('admin.refunds.index')->with([
            'controller' => 'refund',
            'action' => 'index',
        ]);
    }

    /**
     * @param Request $request
     * @param Refund $refund
     *
     * @return RefundResource|View|Factory
     */
    public function show(Request $request, Refund $refund)
    {
        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return new RefundResource($refund);
        }

        return view('admin.refunds.show')->with($this->hydrateData([
            'controller' => 'refund',
            'action' => 'show',
            'data' => new RefundResource($refund),
            'bill' => new BillResource($refund->bill),
            'payment' => new PaymentResource($refund->payment),
            'defaultRedirectUrl' => route('admin.refunds.index'),
        ], $request));
    }

    /**
     * @return View|Factory
     */
    public function create()
    {
        $refund = new Refund;

        return view('admin.refunds.form')->with([
            'controller' => 'refund',
            'action' => 'create',
            'data' => new RefundResource($refund),
        ]);
    }

    /**
     * store function.
     *
     * @param RefundRequest $request
     *
     * @return RedirectResponse
     */
    public function store(RefundRequest $request)
    {
        $this->manager->create($request);

        $request->session()->flash('alert-success', __('admin.Refund was created'));

        return $this->redirectToIndex($request, 'admin.refunds.index');
    }

    /**
     * @param Refund $refund
     *
     * @return View|Factory
     */
    public function edit(Refund $refund)
    {
        return view('admin.refunds.form')->with([
            'controller' => 'refund',
            'action' => 'edit',
            'data' => new RefundResource($refund),
        ]);
    }

    /**
     * @param RefundRequest $request
     * @param Refund $refund
     *
     * @return RedirectResponse
     */
    public function update(RefundRequest $request, Refund $refund)
    {
        $this->manager->update($request, $refund);

        $request->session()->flash('alert-success', __('admin.Refund was updated'));

        return $this->redirectToIndex($request, 'admin.refunds.index');
    }

    /**
     * @param Request $request
     * @param Refund $refund
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Refund $refund)
    {
        $this->manager->delete($refund);

        $request->session()->flash('alert-success', __('admin.Refund was deleted'));

        return redirect()->route('admin.refunds.index');
    }

    /**
     * unclock refund function.
     *
     * @param Refund $refund
     *
     * @return RedirectResponse
     */
    public function unlock_refund(Request $request, Refund $refund)
    {
        if (! $refund->isUnlockRefund()) {
            $request->session()->flash('alert-danger', __('admin.Refund cannot be unlocked'));

            return redirect()->route('admin.bills.show', ['bill' => $refund->bill]);
        }

        $this->manager->unlock_refund($refund);

        $request->session()->flash('alert-success', __('admin.Refund is unlocked'));

        return redirect()->route('admin.bills.show', ['bill' => $refund->bill]);
    }
}
