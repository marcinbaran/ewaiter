<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Select2Trait;
use App\Http\Requests\Admin\OnlinePaymentProviderAccountRequest;
use App\Http\Resources\Admin\OnlinePaymentProviderAccountResource;
use App\Managers\OnlinePaymentProviderAccountManager;
use App\Models\OnlinePaymentProviderAccount;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class OnlinePaymentProviderAccountController extends Controller
{
    use Select2Trait;

    private $manager;

    public function __construct()
    {
        $this->manager = new OnlinePaymentProviderAccountManager();
    }

    public function index()
    {
        return view('admin.online_payment_provider_account.index');
    }

    public function create(Request $request)
    {
        return view('admin.online_payment_provider_account.form')->with($this->hydrateData([
            'controller' => 'online_payment_provider_account',
            'action' => 'create',
            'data' => new OnlinePaymentProviderAccountResource(new OnlinePaymentProviderAccount()),
            'defaultRedirectUrl' => route('admin.online_payment_provider_account.index'),
        ], $request));
    }

    public function store(OnlinePaymentProviderAccountRequest $request)
    {
        $this->manager->store($request);

        $request->session()->flash('alert-success', __('online_payment_provider_account.alert.store'));

        return $this->redirectToIndex($request, 'admin.online_payment_provider_account.index');
    }

    public function delete(OnlinePaymentProviderAccount $onlinePaymentProviderAccount, Request $request)
    {
        $this->manager->delete($onlinePaymentProviderAccount);

        $request->session()->flash('alert-success', __('online_payment_provider_account.alert.delete'));

        return $this->redirectToIndex($request, 'admin.online_payment_provider_account.index');
    }

    public function restaurants()
    {
        $allRestaurantsUsedByOnlinePaymentProviderAccount = OnlinePaymentProviderAccount::all()->pluck('restaurant_id');
        $restaurants = Restaurant::whereNotIn('id', $allRestaurantsUsedByOnlinePaymentProviderAccount)->get();

        $restaurantCollection = collect(json_decode(json_encode($restaurants)));

        return $this->getJsonStringForSelect2($restaurantCollection, collect(), 'id');
    }
}
