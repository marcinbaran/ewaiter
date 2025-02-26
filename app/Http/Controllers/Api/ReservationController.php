<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiExceptions\Reservation\TableReservationDisabled;
use App\Http\Requests\Api\ReservationRequest;
use App\Http\Resources\Api\ReservationResource;
use App\Http\Resources\Api\TableResource;
use App\Managers\ReservationManager;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


/**
 * @OA\Tag(
 *     name="[TENANT] Reservations",
 *     description="[TENANT] API Endpoints for managing Reservations"
 * )
 */
class ReservationController extends ApiController
{
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
        parent::__construct();
        $this->middleware('can:view,reservation', ['only' => ['show']]);
        $this->middleware('can:update,reservation', ['only' => ['update']]);
        $this->middleware('can:delete,reservation', ['only' => ['destroy']]);
        $this->transService = $service;
        $this->manager = new ReservationManager($this->transService);
    }
    /**
     * @OA\Get(
     *     path="/api/reservations",
     *     tags={"[TENANT] Reservations"},
     *     summary="[TENANT] List reservations",
     *     description="Get a paginated list of reservations based on query parameters.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="itemsPerPage",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="List of reservation IDs",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"))
     *     ),
     *     @OA\Parameter(
     *         name="fromDate",
     *         in="query",
     *         description="Start date for filtering",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2024-01-01")
     *     ),
     *     @OA\Parameter(
     *         name="toDate",
     *         in="query",
     *         description="End date for filtering",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2024-01-31")
     *     ),
     *     @OA\Parameter(
     *         name="order[id]",
     *         in="query",
     *         description="Sort order for reservation ID",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example="asc")
     *     ),
     *     @OA\Parameter(
     *         name="order[name]",
     *         in="query",
     *         description="Sort order for reservation name",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example="desc")
     *     ),
     *     @OA\Parameter(
     *         name="order[date]",
     *         in="query",
     *         description="Sort order for reservation date",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example="desc")
     *     ),
     *     @OA\Parameter(
     *         name="order[status]",
     *         in="query",
     *         description="Sort order for reservation status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example="asc")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of reservations",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ReservationResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */

    public function index(ReservationRequest $request): AnonymousResourceCollection
    {

        $limit = $request->query->get('itemsPerPage', ReservationResource::LIMIT);
        $offset = ($request->query->get('page', 1) - 1) * $limit;
        $orderBy = $request->query('orderBy', 'id');
        $orderDirection = $request->query('orderDirection', 'desc');
        $order = $request->input('order', [$orderBy => $orderDirection]);
        $user = Auth::user();

        $criteria = [
            'id' => (array)$request->id,
            'noLimit' => $request->query->get('noLimit', false),
            'fromDate' => $request->fromDate,
            'toDate' => $request->toDate,
        ];

        $reservationResource = ReservationResource::collection(Reservation::getRows($criteria, $order, $limit, $offset));

        $reservationResource->additional['page'] = $request->query->get('page', 1);
        $reservationResource->additional['itemsPerPage'] = $limit;
        $criteria['noLimit'] = true;
        $reservationResource->additional['totalPages'] = ceil(Reservation::getRows($criteria, $order, $limit, $offset)->count() / $limit);
        $reservationResource->additional['orderBy'] = $orderBy;
        $reservationResource->additional['orderDirection'] = $orderDirection;

        return $reservationResource;
    }
    /**
     * @OA\Get(
     *     path="api/reservations",
     *     tags={"Reservations"},
     *     summary="List all reservations for specific user",
     *     description="Get a paginated list of all reservations including those made by the user.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="itemsPerPage",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="List of reservation IDs",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"))
     *     ),
     *     @OA\Parameter(
     *         name="fromDate",
     *         in="query",
     *         description="Start date for filtering",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2024-01-01")
     *     ),
     *     @OA\Parameter(
     *         name="toDate",
     *         in="query",
     *         description="End date for filtering",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2024-01-31")
     *     ),
     *     @OA\Parameter(
     *         name="orderDirection",
     *         in="query",
     *         description="Sort order for reservation",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example="asc")
     *     ),
     *     @OA\Parameter(
     *         name="orderBy",
     *         in="query",
     *         description="Sort order for reservation name",
     *         required=false,
     *         @OA\Schema(type="string", enum={"created_at", "updated_at", "id", "name", "date", "status"}, example="desc")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of reservations",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ReservationResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function index_all(ReservationRequest $request) //: AnonymousResourceCollection
    {
        $limit = $request->query->get('itemsPerPage', ReservationResource::LIMIT);
        $offset = ($request->query->get('page', 1) - 1) * $limit;
        $orderBy = $request->query('orderBy', 'id');
        $orderDirection = $request->query('orderDirection', 'desc');
        $order = $request->input('order', [$orderBy => $orderDirection]);
        $user = Auth::user();

        $criteria = [
            'id' => (array)$request->id,
            'noLimit' => $request->query->get('noLimit', false),
            'fromDate' => $request->fromDate,
            'toDate' => $request->toDate,
        ];

        $criteria['user'] = (array)($user->isEndUserRole() ? [$user->id] : $request->user);

        $reservationResource = ReservationResource::collection(Reservation::getRestaurantsReservations($criteria, $order, $limit, $offset));

        $reservationResource->additional['page'] = $request->query->get('page', 1);
        $reservationResource->additional['itemsPerPage'] = $limit;
        $criteria['noLimit'] = true;
        $reservationResource->additional['totalPages'] = ceil(Reservation::getRestaurantsReservations($criteria, $order, $limit, $offset)->count() / $limit);
        $reservationResource->additional['orderBy'] = $orderBy;
        $reservationResource->additional['orderDirection'] = $orderDirection;

        return $reservationResource;
    }

    /**
     * @OA\Get(
     *     path="/reservations/{reservationId}",
     *     tags={"[TENANT] Reservations"},
     *     summary="[TENANT] Get reservation details",
     *     description="Retrieve details of a specific reservation by ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="reservationId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reservation details",
     *         @OA\JsonContent(ref="#/components/schemas/ReservationResource")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Reservation not found"
     *     )
     * )
     */
    public function show(Reservation $reservation): ReservationResource
    {
        return new ReservationResource($reservation);
    }

    /**
     * @OA\Post(
     *     path="/reservations",
     *     tags={"[TENANT] Reservations"},
     *     summary="[TENANT] Create a new reservation",
     *     description="Create a new reservation with the provided details.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ReservationRequestPOST")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Reservation created",
     *         @OA\JsonContent(ref="#/components/schemas/ReservationResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function store(ReservationRequest $request): ReservationResource
    {
        $restaurant = Restaurant::getCurrentRestaurant();

        if ($restaurant->table_reservation_active == false) {
            throw new TableReservationDisabled();
        }

        $user = Auth::user();
        throw_if($user->guest, new AccessDeniedHttpException(__('admin.Action prohibited')));

        //throw_if($this->manager->checkTableInAvailability($request), new AccessDeniedHttpException(__('reservations.The selected table has been already reserved')));
        throw_if($this->manager->checkTableTooSmall($request), new AccessDeniedHttpException(__('reservations.The selected table is too small')));
        return (new ReservationResource($this->manager->create($request)->fresh()))->withStatusCode(201);
    }

    /**
     * @OA\Put(
     *     path="/reservations/{reservationId}",
     *     tags={"[TENANT] Reservations"},
     *     summary="[TENANT] Update a reservation",
     *     description="Update the details of an existing reservation.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="reservationId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ReservationRequestPUT")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reservation updated",
     *         @OA\JsonContent(ref="#/components/schemas/ReservationResource")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Reservation not found"
     *     )
     * )
     */
    public function update(ReservationRequest $request, Reservation $reservation): ReservationResource
    {
        $restaurant = Restaurant::getCurrentRestaurant();

        if ($restaurant->table_reservation_active == false) {
            throw new TableReservationDisabled();
        }

        $user = Auth::user();
        throw_if($user->guest, new AccessDeniedHttpException(__('admin.Action prohibited')));
        throw_if($user->isEndUserRole() && $user->id != $reservation->user_id, new AccessDeniedHttpException(__('admin.Action prohibited')));

        return new ReservationResource($this->manager->update($request, $reservation)->fresh());
    }

    /**
     * @OA\Delete(
     *     path="/reservations/{reservationId}",
     *     tags={"[TENANT] Reservations"},
     *     summary="[TENANT] Delete a reservation",
     *     description="Delete an existing reservation by ID.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="reservationId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reservation deleted",
     *         @OA\JsonContent(ref="#/components/schemas/ReservationResource")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Reservation not found"
     *     )
     * )
     */
    public function destroy(Reservation $reservation): ReservationResource
    {
        return new ReservationResource($this->manager->delete($reservation));
    }

    /**
     * @OA\Get(
     *     path="/reservation/free",
     *     tags={"[TENANT] Reservations"},
     *     summary="[TENANT]List free tables",
     *     description="Get a list of free tables based on query parameters.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="itemsPerPage",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Date for checking table availability",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2024-01-01")
     *     ),
     *     @OA\Parameter(
     *         name="order[id]",
     *         in="query",
     *         description="Sort order for table ID",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example="asc")
     *     ),
     *     @OA\Parameter(
     *         name="order[name]",
     *         in="query",
     *         description="Sort order for table name",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"}, example="desc")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of free tables",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/TableResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function free(Request $request): AnonymousResourceCollection
    {
        $user = Auth::user();
        throw_if($user->guest, new AccessDeniedHttpException(__('admin.Action prohibited')));

        $limit = $request->query->get('itemsPerPage', TableResource::LIMIT);
        $offset = ($request->query->get('page', 1) - 1) * $limit;
        $order = $request->input('order', ['id' => 'asc']);

        $criteria = [
            'active' => 1,
            'date' => $request->get('date', date('Y-m-d')),
            'noLimit' => $request->query->get('noLimit', false),
        ];

        return TableResource::collection(Reservation::getFree($criteria, $limit, $offset));
    }
}
