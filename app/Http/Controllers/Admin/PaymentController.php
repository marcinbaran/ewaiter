<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentRequest;
use App\Http\Resources\Admin\BillResource;
use App\Http\Resources\Admin\PaymentResource;
use App\Http\Resources\Admin\RefundResource;
use App\Managers\PaymentManager;
use App\Models\Payment;
use App\Services\TranslationService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var PaymentManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        $this->transService = $service;
        $this->manager = new PaymentManager($this->transService);
        PaymentResource::wrap('results');
    }

    /**
     * @param Request $request
     *
     * @return View|Factory|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        return view('admin.payments.index')->with([
            'controller' => 'payment',
            'action' => 'index',
        ]);
    }

    /**
     * @param Request $request
     * @param Payment $payment
     *
     * @return PaymentResource|View|Factory
     */
    public function show(Request $request, Payment $payment)
    {
        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return new PaymentResource($payment);
        }

        return view('admin.payments.show')->with($this->hydrateData([
            'controller' => 'payment',
            'action' => 'show',
            'data' => new PaymentResource($payment),
            'bill' => new BillResource($payment->bill),
            'refund' => new RefundResource($payment->refund),
            'defaultRedirectUrl' => route('admin.payments.index'),
        ], $request));
    }

    /**
     * @return View|Factory
     */
    public function create()
    {
        $payment = new Payment;

        return view('admin.payments.form')->with([
            'controller' => 'payment',
            'action' => 'create',
            'data' => new PaymentResource($payment),
        ]);
    }

    /**
     * store function.
     *
     * @param PaymentRequest $request
     *
     * @return RedirectResponse
     */
    public function store(PaymentRequest $request)
    {
        $this->manager->createFromRequest($request);

        $request->session()->flash('alert-success', __('admin.Payment was created'));

        return $this->redirectToIndex($request, 'admin.payments.index');
    }

    /**
     * @param Payment $payment
     *
     * @return View|Factory
     */
    public function edit(Payment $payment)
    {
        return view('admin.payments.form')->with([
            'controller' => 'payment',
            'action' => 'edit',
            'data' => new PaymentResource($payment),
        ]);
    }

    /**
     * @param PaymentRequest $request
     * @param Payment $payment
     *
     * @return RedirectResponse
     */
    public function update(PaymentRequest $request, Payment $payment)
    {
        $this->manager->updateFromRequest($request, $payment);

        $request->session()->flash('alert-success', __('admin.Payment was updated'));

        return $this->redirectToIndex($request, 'admin.payments.index');
    }

    /**
     * @param Request $request
     * @param Payment $payment
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Payment $payment)
    {
        $this->manager->delete($payment);

        $request->session()->flash('alert-success', __('admin.Payment was deleted'));

        return redirect()->route('admin.payments.index');
    }
}
