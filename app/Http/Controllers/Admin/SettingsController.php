<?php

namespace App\Http\Controllers\Admin;

use App\Collections\SettingsCollection;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OnlinePaymentProviderAccountRequest;
use App\Http\Requests\Admin\SettingsRequest;
use App\Http\Resources\Admin\SettingsResource;
use App\Managers\OnlinePaymentProviderAccountManager;
use App\Managers\SettingsManager;
use App\Models\OnlinePaymentProviderAccount;
use App\Models\Restaurant;
use App\Models\Settings;
use App\Models\User;
use App\Services\TpayService;
use App\Services\TranslationService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SettingsController extends Controller
{
    const TPAY_ACCOUNT = 'konto_tpay';

    public function __construct(
        private readonly TranslationService                  $transService,
        private readonly SettingsCollection                  $settingsCollection,
        private readonly OnlinePaymentProviderAccountManager $PaymentManager
    )
    {

        $this->manager = new SettingsManager($this->transService);
        SettingsResource::wrap('results');
    }


    /**
     * @param Request $request
     *
     * @return View|Factory|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        return redirect(route('admin.settings.edit', ['settings' => Settings::query()->orderBy('id', 'asc')->first()->id]));
    }

    /**
     * @param Settings $settings
     *
     * @return View|Factory
     */
    public function edit(Settings $settings)
    {
        $restaurant = Restaurant::getCurrentRestaurant();

        if ($settings->key === self::TPAY_ACCOUNT) {
            $account = $this->PaymentManager->show($restaurant->id);
            $settings->value = $account;
        }

        $isTpayDisabled = $restaurant ? !OnlinePaymentProviderAccount::where('restaurant_id', $restaurant->id)?->first()?->exists() : false;

        return view('admin.settings.index')->with([
            'controller' => 'settings',
            'action' => 'edit',
            'data' => new SettingsResource($settings),
            'settings' => $this->settingsCollection,
            'tpayDisabled' => $isTpayDisabled ?? false,
        ]);
    }

    /**
     * @param Request $request
     * @param Settings $settings
     *
     * @return SettingsrResource|View|Factory
     */
    public function show(Request $request, Settings $settings)
    {
        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return new SettingsResource($settings);
        }

        return view('admin.settings.show')->with([
            'controller' => 'settings',
            'action' => 'show',
            'data' => new SettingsResource($settings),
        ]);
    }

    /**
     * @param Request $request
     * @param Settings $settings
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Settings $settings)
    {
        $this->manager->delete($settings);

        $request->session()->flash('alert-success', __('admin.Settings was deleted'));

        return redirect()->route('admin.settings.index');
    }

    public function modal_delivery(Request $request)
    {
        $user = Auth::user();
        if (!$user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN])) {
            return json_encode([
                'status' => 400,
                'error' => gtrans('admin.Action prohibited'),
            ]);
        }
        $settings = Settings::where('key', 'rodzaje_dostawy')->first();
        if (!$settings) {
            return json_encode([
                'status' => 400,
                'error' => gtrans('admin.Action prohibited'),
            ]);
        }
        $data = view('admin.settings.partials.delivery_modal')->with([
            'data' => $settings,
        ])->render();

        return json_encode([
            'status' => 200,
            'data' => $data,
        ]);
    }

    public function delivery_store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN])) {
            return json_encode([
                'status' => 400,
                'error' => gtrans('admin.Action prohibited'),
            ]);
        }
        $validation = Validator::make($request->all(), [
            'value_active' => 'required',
        ]);

        if ($validation->fails()) {
            return json_encode(['errors' => $validation->errors()]);
        }
        $value_active_array = [];
        foreach ($user->getLocales() as $lang) {
            foreach ($request->get('value_active') as $value => $value_active) {
                $value_active_array[$lang][$value] = $value_active;
            }
        }
        Settings::where('key', 'rodzaje_dostawy')->update(['value_active' => json_encode($value_active_array)]);

        $request->session()->flash('alert-success', __('admin.Delivery options changed'));

        return redirect()->route('admin.bills.index');
    }

    /**
     * @param SettingsRequest $request
     * @param Settings $settings
     *
     * @return RedirectResponse
     */
    public function update(SettingsRequest $request, Settings $settings)
    {
        $this->manager->update($request, $settings, true);

        $request->session()->flash('alert-success', __('admin.Settings was updated'));

        return redirect()->route('admin.settings.edit', ['settings' => $settings->id]);
    }

    public function createAsTenant(OnlinePaymentProviderAccountRequest $request, Settings $settings)
    {
        $request->merge([
            'login' => trim($request->input('login')),
            'password' => trim($request->input('password')),
            'api_key' => trim($request->input('api_key')),
            'api_password' => trim($request->input('api_password')),
        ]);
        $validation = Validator::make($request->all(), [
            'login' => 'required|min:3|max:255',
            'password' => 'required|min:3|max:255',
            'api_key' => 'required|min:3|max:255',
            'api_password' => 'required|min:3|max:255',
        ]);

        if (!$this->checkCredential($request)) {
            $request->session()->flash('alert-danger', __('admin.The user credentials were incorrect'));

            return redirect()->route('admin.settings.edit', ['settings' => $settings->id]);
        }

        if (!$validation->fails()) {
            $restaurant = Restaurant::getCurrentRestaurant();
            $request->merge(['restaurant_id' => "$restaurant->id"]);
            $this->PaymentManager->store($request);
            $request->session()->flash('alert-success', __('online_payment_provider_account.alert.store'));
        } else {
            $request->session()->flash('alert-danger', $validation->errors()->first());
        }

        return redirect()->route('admin.settings.edit', ['settings' => $settings->id]);
    }

    /**
     * store function.
     *
     * @param SettingsRequest $request
     *
     * @return RedirectResponse
     */
    public function store(SettingsRequest $request)
    {
        $this->manager->create($request);

        $request->session()->flash('alert-success', __('admin.Settings was created'));
        $currentRoute = request()->getRequestUri();

        return redirect()->route($currentRoute);
    }

    /**
     * @return View|Factory
     */
    public function create()
    {
        $settings = new Settings;

        return view('admin.settings.index')->with([
            'controller' => 'settings',
            'action' => 'create',
            'data' => new SettingsResource($settings),
            'settings' => $this->settingsCollection,
        ]);
    }

    private function checkCredential(OnlinePaymentProviderAccountRequest $request): bool
    {
        $tPayService = new TpayService(
            (int) $request->input('login'),
            $request->input('password'),
            $request->input('api_key'),
            $request->input('api_password')
        );

        return $tPayService->testCredentials();
    }
}
