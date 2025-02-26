<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\TrWithdrawResource;
use App\Managers\TrWithdrawManager;
use App\Models\TrWithdrawal;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\View\View;

class TrWithdrawController extends Controller
{
    /**
     * @var TrWithdrawManager
     */
    private $manager;

    public function __construct()
    {
        $this->manager = new TrWithdrawManager();
        TrWithdrawResource::wrap('results');
    }

    /**
     * @param Request $request
     *
     * @return View|Factory|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return TrWithdrawResource::collection(TrWithdrawal::getPaginatedForPanel($request->get('query_tr_withdraw'), TrWithdrawResource::LIMIT, ['id' => 'desc'], null));
        }

        return view('admin.tr_withdraws.index')->with([
            'controller' => 'tr_withdraw',
            'action' => 'index',
        ]);
    }

    /**
     * @param Request  $request
     * @param TrWithdrawal $tr_withdraw
     *
     * @return TrWithdrawResource|View|Factory
     */
    public function show(Request $request, TrWithdrawal $tr_withdraw)
    {
        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return new TrWithdrawResource($tr_withdraw);
        }

        return view('admin.tr_withdraws.show')->with([
            'controller' => 'tr_withdraw',
            'action' => 'show',
            'data' => new TrWithdrawResource($tr_withdraw),
        ]);
    }
}
