<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoomRequest;
use App\Http\Resources\Admin\RoomResource;
use App\Managers\RoomManager;
use App\Models\Room;
use App\Services\TranslationService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\View\View;

class RoomController extends Controller
{
    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var RoomManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        $this->transService = $service;
        $this->manager = new RoomManager($this->transService);
        RoomResource::wrap('results');
    }

    /**
     * @param Request $request
     *
     * @return View|Factory|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return RoomResource::collection(Room::getPaginatedForPanel($request->get('query_room'), RoomResource::LIMIT, ['name' => 'asc']));
        }

        return view('admin.rooms.index')->with([
            'controller' => 'room',
            'action' => 'index',
        ]);
    }

    /**
     * @param Request $request
     * @param Room $room
     *
     * @return RoomResource|View|Factory
     */
    public function show(Request $request, Room $room)
    {
        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return new RoomResource($room);
        }

        return view('admin.rooms.show')->with([
            'controller' => 'room',
            'action' => 'show',
            'data' => new RoomResource($room),
        ]);
    }

    /**
     * @return View|Factory
     */
    public function create(Request $request)
    {
        $room = new Room;

        return view('admin.rooms.form')->with($this->hydrateData([
            'controller' => 'room',
            'action' => 'create',
            'data' => new RoomResource($room),
            'defaultRedirectUrl' => route('admin.rooms.index'),
        ], $request));
    }

    /**
     * store function.
     *
     * @param RoomRequest $request
     *
     * @return RedirectResponse
     */
    public function store(RoomRequest $request)
    {
        $this->manager->create($request);

        $request->session()->flash('alert-success', __('admin.Room was created'));

        return $this->redirectToIndex($request, 'admin.rooms.index');
    }

    /**
     * @param Room $room
     *
     * @return View|Factory
     */
    public function edit(Request $request, Room $room)
    {
        return view('admin.rooms.form')->with($this->hydrateData([
            'controller' => 'room',
            'action' => 'edit',
            'data' => new RoomResource($room),
            'defaultRedirectUrl' => route('admin.rooms.index'),
        ], $request));
    }

    /**
     * @param RoomRequest $request
     * @param Room $room
     *
     * @return RedirectResponse
     */
    public function update(RoomRequest $request, Room $room)
    {
        $isRoomNumberChanged = $this->manager->isTableNumberChanged($request, $room);

        $this->manager->update($request, $room, true);

        $request->session()->flash('alert-success', $isRoomNumberChanged ? __('admin.rooms.room_data_changed_you_need_to_regenerate_qr_code') : __('admin.Room was updated'));

        return $this->redirectToIndex($request, 'admin.rooms.index');
    }

    /**
     * @param Request $request
     * @param Room $room
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Room $room)
    {
        $this->manager->delete($room);

        $request->session()->flash('alert-success', __('admin.Room was deleted'));

        return redirect()->route('admin.rooms.index');
    }
}
