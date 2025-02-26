<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Select2Trait;
use App\Http\Requests\Admin\ReservationRequest;
use App\Http\Resources\Admin\ReservationResource;
use App\Http\Resources\Admin\TableResource;
use App\Http\Resources\Admin\UserResource;
use App\Managers\ReservationManager;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\Table;
use App\Models\UserSystem;
use App\Services\TranslationService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\View\View;

class ReservationController extends Controller
{
    use Select2Trait;

    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var ReservationManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        $this->transService = $service;
        $this->manager = new ReservationManager($this->transService);
        ReservationResource::wrap('results');
    }

    /**
     * @param Request $request
     *
     * @return View|Factory|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        return view('admin.reservations.index')->with([
            'controller' => 'reservation',
            'action' => 'index',
        ]);
    }

    /**
     * @param Request $request
     * @param Reservation $reservation
     *
     * @return ReservationResource|View|Factory
     */
    public function show(Request $request, Reservation $reservation)
    {
        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return new ReservationResource($reservation);
        }

        return view('admin.reservations.show')->with([
            'controller' => 'reservation',
            'action' => 'show',
            'data' => new ReservationResource($reservation),
        ]);
    }


    public function create(Request $request)
    {
        if ($this->checkIsTableReservationEnabled()) {
            $request->session()->flash('alert-danger', __('admin.reservation_disable'));

            return $this->redirectToIndex($request, 'admin.reservations.index');
        }

        $reservation = new Reservation;

        return view('admin.reservations.form')->with($this->hydrateData([
            'controller' => 'reservation',
            'action' => 'create',
            'data' => new ReservationResource($reservation),
            'oldTables' => old('table_id'),
            'oldUsers' => old('user_id'),
            'oldStatuses' => old('status'),
            'defaultRedirectUrl' => route('admin.reservations.index'),
        ], $request));
    }

    /**
     * store function.
     *
     * @param ReservationRequest $request
     *
     * @return RedirectResponse
     */
    public function store(ReservationRequest $request)
    {
        $this->manager->create($request);

        $request->session()->flash('alert-success', __('admin.Reservation was created'));

        return $this->redirectToIndex($request, 'admin.reservations.index');
    }

    /**
     * @param Reservation $reservation
     */
    public function edit(Request $request, Reservation $reservation)
    {
        if($this->checkIsTableReservationEnabled()) {
            $request->session()->flash('alert-danger', __('admin.reservation_disable'));

            return $this->redirectToIndex($request, 'admin.reservations.index');
        }

        return view('admin.reservations.form')->with($this->hydrateData([
            'controller' => 'reservation',
            'action' => 'edit',
            'data' => new ReservationResource($reservation),
            'oldTables' => old('table_id'),
            'oldUsers' => old('user_id'),
            'oldStatuses' => old('status'),
            'defaultRedirectUrl' => route('admin.reservations.index'),
        ], $request));
    }

    /**
     * @param ReservationRequest $request
     * @param Reservation $reservation
     *
     * @return RedirectResponse
     */
    public function update(ReservationRequest $request, Reservation $reservation)
    {
        if ($request->get('status') == 1 && ! $this->manager->checkAvailability($request, $reservation)) {
            $request->session()->flash('alert-danger', __('admin.Reservation cannot be confirmed, table has been already reserved'));

            return $this->redirectToIndex($request, 'admin.reservations.index');
        }

        $this->manager->update($request, $reservation);

        $request->session()->flash('alert-success', __('admin.Reservation was updated'));

        return $this->redirectToIndex($request, 'admin.reservations.index');
    }

    /**
     * @param Request $request
     * @param Reservation $reservation
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Reservation $reservation)
    {
        $this->manager->delete($reservation);

        $request->session()->flash('alert-success', __('admin.Reservation was deleted'));

        return redirect()->route('admin.reservations.index');
    }

    public function tables(?int $id = null)
    {
        $reservationResource = new ReservationResource($id ? Reservation::findOrFail($id) : new Reservation);
        $tables = TableResource::collection(Table::where('active', 1)->get());
        $selectedTable = collect([
            ['id' => $reservationResource->table_id],
        ]);
        $response = $this->getJsonStringForSelect2($tables, $selectedTable, 'id');

        return $response;
    }

    public function users(?int $id = null)
    {
        $reservationResource = new ReservationResource($id ? Reservation::findOrFail($id) : new Reservation);
        $users = UserResource::collection(UserSystem::where('activated', 1)->where('blocked', 0)->where('roles', 'not like', '%ROLE_GUEST%')->get());
        $selectedUser = collect([
            ['id' => $reservationResource->user_id],
        ]);
        $response = $this->getJsonStringForSelect2($users, $selectedUser, 'id', function (UserResource $user) {
            return $user->first_name.' ('.$user->email.')';
        });

        return $response;
    }

    public function statuses(?int $id = null)
    {
        $reservationResource = new ReservationResource($id ? Reservation::findOrFail($id) : new Reservation);
        $statuses = collect(json_decode(json_encode([
            ['id' => 0, 'name' => __('admin.reservations.statuses.pending')],
            ['id' => 1, 'name' => __('admin.reservations.statuses.confirmed')],
            ['id' => 2, 'name' => __('admin.reservations.statuses.cancelled')],
        ])));
        $selectedStatus = collect([
            ['id' => $reservationResource->status],
        ]);
        $response = $this->getJsonStringForSelect2($statuses, $selectedStatus, 'id');

        return $response;
    }

    private function checkIsTableReservationEnabled(): bool
    {
        $restaurant = Restaurant::getCurrentRestaurant();

        return $restaurant->table_reservation_active == false;
    }
}
