<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Select2Trait;
use App\Http\Requests\Admin\RestaurantRequest;
use App\Http\Resources\Admin\RestaurantResource;
use App\Http\Resources\Admin\RestaurantTagResource;
use App\Managers\RestaurantManager;
use App\Models\Address;
use App\Models\Restaurant;
use App\Models\RestaurantTag;
use App\Models\User;
use App\Services\TranslationService;
use Hyn\Tenancy\Environment;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use function Symfony\Component\VarDumper\Dumper\esc;

class RestaurantController extends Controller
{
    use Select2Trait;

    /**
     * @var TranslationService
     */
    private $transService;

    private $name = 'restaurant';

    /**
     * @var RestaurantManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        $this->transService = $service;
        $this->manager = new RestaurantManager($this->transService);
        RestaurantResource::wrap('results');
    }

    /**
     * @param Request $request
     *
     * @return View|Factory|AnonymousResourceCollection
     */
    public function index()
    {
        return view('admin.restaurants.index')->with([
            'controller' => $this->name,
        ]);
    }

    /**
     * @param Request $request
     * @param Restaurant $restaurant
     *
     * @return RestaurantResource|View|Factory
     */
    public function show(Request $request, Restaurant $restaurant)
    {
        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return new RestaurantResource($restaurant);
        }

        return view('admin.restaurants.show')->with([
            'controller' => 'order',
            'action' => 'show',
            'data' => new RestaurantResource($restaurant),
        ]);
    }

    /**
     * store function.
     *
     * @param RestaurantRequest $request
     *
     * @return RedirectResponse
     */
    public function store(RestaurantRequest $request)
    {
        $this->manager->create($request);

        $request->session()->flash('alert-success', __('admin.Restaurant was created'));

        return $this->redirectToIndex($request, 'admin.restaurants.index');
    }

    /**
     * @return View|Factory
     */
    public function create(Request $request)
    {
        $restaurant = new Restaurant;
        $restaurant->address = new Address;
        return view('admin.restaurants.form')->with($this->hydrateData([
            'controller' => 'order',
            'action' => 'create',
            'data' => new RestaurantResource($restaurant),
            'oldRestaurantTags' => $this->getOldArrayForSelect2('tag_checkbox', 'id'),
            'defaultRedirectUrl' => route('admin.restaurants.index'),
        ], $request));
    }

    /**
     * @param Restaurant $restaurant
     *
     * @return View|Factory
     */
    public function edit(Request $request, int $id)
    {
        $restaurant = Restaurant::with('restaurant_tags')->findOrFail($id);

        if (! isset($restaurant->address)) {
            $restaurant->address = new Address();
        }

        return view('admin.restaurants.form')->with($this->hydrateData([
            'controller' => 'order',
            'action' => 'edit',
            'data' => new RestaurantResource($restaurant),
            'oldRestaurantTags' => $this->getOldArrayForSelect2('tag_checkbox', 'id'),
            'defaultRedirectUrl' => route('admin.restaurants.index'),
        ], $request));
    }

    /**
     * @param RestaurantRequest $request
     * @param Restaurant $restaurant
     *
     * @return RedirectResponse
     */
    public function update(RestaurantRequest $request, Restaurant $restaurant)
    {
        $this->manager->update($request, $restaurant);
        $request->session()->flash('alert-success', __('admin.Restaurant was updated'));
        return $this->redirectToIndex($request, 'admin.restaurants.index');

    }

    /**
     * @param Request $request
     * @param Restaurant $restaurant
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Restaurant $restaurant)
    {
        $this->manager->delete($restaurant);

        $request->session()->flash('alert-success', __('admin.Restaurant was deleted'));

        return redirect()->route('admin.restaurants.index');
    }

    public function login(Request $request, Restaurant $restaurant)
    {
        $website = \Hyn\Tenancy\Models\Website::where('uuid', $restaurant->hostname)->first();
        $hostname = \Hyn\Tenancy\Models\Hostname::where('website_id', $website->id)->first();

        $website = \Hyn\Tenancy\Facades\TenancyFacade::website();

        app(Environment::class)->tenant($hostname->website);

        $user = User::where('email', 'admin@primebitstudio.com')->orWhere(
            'email',
            'aplikacje@zetorzeszow.pl'
        )->orderBy('email')->first();
        $token = md5(microtime().'WKAUTH');
        $user->remember_token = $token;
        $user->save();

        return redirect()->away('http://'.$hostname->fqdn.'/login_admin/'.$token);
    }

    public function restaurant_tags(?int $id = null)
    {
        $restaurantResource = new RestaurantResource($id ? Restaurant::findOrFail($id) : new Restaurant);
        $restaurant_tags = RestaurantTagResource::collection(RestaurantTag::all());
        $response = $this->getJsonStringForSelect2($restaurant_tags, $restaurantResource->restaurant_tags, 'tag_id', function (RestaurantTagResource $restaurant_tag) {
            return $restaurant_tag->value['pl'] ?? ' ';
        });

        return $response;
    }
}
