<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Select2Trait;
use App\Http\Requests\Admin\QRCodeRequest;
use App\Http\Resources\Admin\QRCodeResource;
use App\Http\Resources\Admin\RoomResource;
use App\Http\Resources\Admin\TableResource;
use App\Managers\QRCodeManager;
use App\Models\QRCode;
use App\Models\Room;
use App\Models\Table;
use App\Services\TranslationService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\View\View;

class QRCodeController extends Controller
{
    use Select2Trait;

    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var QRCodeManager
     */
    private QRCodeManager $manager;

    public function __construct(TranslationService $service)
    {
        $this->transService = $service;
        $this->manager = new QRCodeManager($this->transService);
        QRCodeResource::wrap('results');
    }

    /**
     * @param Request $request
     *
     * @return View|Factory|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return QRCodeResource::collection(QRCode::getPaginatedForPanel($request->get('query_qr_code'), QRCodeResource::LIMIT, ['id' => 'desc']));
        }

        return view('admin.qr_codes.index')->with([
            'controller' => 'qr_code',
            'action' => 'index',
        ]);
    }

    /**
     * @param Request $request
     * @param QRCode $qr_code
     *
     * @return QRCodeResource|View|Factory
     */
    public function show(Request $request, QRCode $qr_code)
    {
        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return new QRCodeResource($qr_code);
        }

        return view('admin.qr_codes.show')->with([
            'controller' => 'qr_code',
            'action' => 'show',
            'data' => new QRCodeResource($qr_code),
        ]);
    }

    /**
     * @return View|Factory
     */
    public function create(Request $request)
    {
        $qr_code = new QRCode;

        return view('admin.qr_codes.form')->with($this->hydrateData([
            'controller' => 'qr_code',
            'action' => 'create',
            'data' => new QRCodeResource($qr_code),
            'oldRooms' => old('object_id_room'),
            'oldTables' => old('object_id_table'),
            'defaultRedirectUrl' => route('admin.qr_codes.index'),
        ], $request));
    }

    /**
     * store function.
     *
     * @param QRCodeRequest $request
     *
     * @return RedirectResponse
     */
    public function store(QRCodeRequest $request)
    {
        $this->manager->create($request);

        $request->session()->flash('alert-success', __('admin.QRCode was created'));

        return $this->redirectToIndex($request, 'admin.qr_codes.index');
    }

    /**
     * @param QRCode $qr_code
     *
     * @return View|Factory
     */
    public function edit(Request $request, QRCode $qr_code)
    {
        return view('admin.qr_codes.form')->with($this->hydrateData([
            'controller' => 'qr_code',
            'action' => 'edit',
            'data' => new QRCodeResource($qr_code),
            'oldRooms' => old('object_id_room'),
            'oldTables' => old('object_id_table'),
            'defaultRedirectUrl' => route('admin.qr_codes.index'),
        ], $request));
    }

    /**
     * @param QRCodeRequest $request
     * @param QRCode $qr_code
     *
     * @return RedirectResponse
     */
    public function update(QRCodeRequest $request, QRCode $qr_code)
    {
        $this->manager->update($request, $qr_code, true);

        $request->session()->flash('alert-success', __('admin.QRCode was updated'));

        return $this->redirectToIndex($request, 'admin.qr_codes.index');
    }

    /**
     * @param Request $request
     * @param QRCode $qr_code
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, QRCode $qr_code)
    {
        $this->manager->delete($qr_code);

        $request->session()->flash('alert-success', __('admin.QRCode was deleted'));

        return redirect()->route('admin.qr_codes.index');
    }

    public function rooms(?int $id = null)
    {
        $qrCodeResource = new QRCodeResource($id ? QRCode::findOrFail($id) : new QRCode);
        $rooms = RoomResource::collection(Room::all());
        $selectedRooms = collect([
            ['id' => $qrCodeResource->object_type == 'room' ? $qrCodeResource->object_id : null],
        ]);
        $response = $this->getJsonStringForSelect2($rooms, $selectedRooms, 'id');

        return $response;
    }

    public function tables(?int $id = null)
    {
        $qrCodeResource = new QRCodeResource($id ? QRCode::findOrFail($id) : new QRCode);
        $tables = TableResource::collection(Table::all());
        $selectedTables = collect([
            ['id' => $qrCodeResource->object_type == 'table' ? $qrCodeResource->object_id : null],
        ]);
        $response = $this->getJsonStringForSelect2($tables, $selectedTables, 'id');

        return $response;
    }
}
