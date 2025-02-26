<?php

namespace App\Http\Controllers\Admin;

use App\Enum\Voucher\VoucherAddingType;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Select2Trait;
use App\Http\Requests\Admin\VoucherRequest;
use App\Http\Resources\Admin\VoucherResource;
use App\Managers\VoucherManager;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    use Select2Trait;

    private $manager;

    public function __construct()
    {
        $this->manager = new VoucherManager();
    }

    public function index()
    {
        return view('admin.voucher.index');
    }

    public function create(Request $request)
    {
        return view('admin.voucher.form')->with($this->hydrateData([
            'controller' => 'voucher',
            'action' => 'create',
            'data' => new VoucherResource(new Voucher()),
            'defaultRedirectUrl' => route('admin.vouchers.index'),
        ], $request));
    }

    public function store(VoucherRequest $request)
    {
        $this->manager->store($request);

        $request->session()->flash('alert-success', __('voucher.alert.store'));

        return $this->redirectToIndex($request, 'admin.vouchers.index');
    }

    public function edit(Voucher $voucher, Request $request)
    {
        return view('admin.voucher.form')->with($this->hydrateData([
            'controller' => 'voucher',
            'action' => 'edit',
            'data' => new VoucherResource($voucher),
            'defaultRedirectUrl' => route('admin.vouchers.index'),
        ], $request));
    }

    public function update(Voucher $voucher, VoucherRequest $request)
    {
        $this->manager->update($voucher, $request);

        $request->session()->flash('alert-success', __('voucher.alert.update'));

        return $this->redirectToIndex($request, 'admin.vouchers.index');
    }

    public function delete(Voucher $voucher, Request $request)
    {
        $voucher->delete();

        $request->session()->flash('alert-success', __('voucher.alert.delete'));

        return $this->redirectToIndex($request, 'admin.vouchers.index');
    }

    public function addingTypes()
    {
        $voucherAddingTypes = collect(json_decode(json_encode($this->transformEnumKeyValuePairsForSelect2(VoucherAddingType::getKeyValuePairs(), 'voucher.adding_type'))));

        return $this->getJsonStringForSelect2($voucherAddingTypes, collect(), 'id');
    }
}
