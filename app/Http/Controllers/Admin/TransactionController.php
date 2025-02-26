<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\TransactionResource;
use App\Managers\TransactionManager;
use App\Models\Transaction;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * @var TransactionManager
     */
    private $manager;

    public function __construct()
    {
        $this->manager = new TransactionManager();
        TransactionResource::wrap('results');
    }

    /**
     * @param Request $request
     *
     * @return View|Factory|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return TransactionResource::collection(Transaction::getPaginatedForPanel($request->get('query_transaction'), TransactionResource::LIMIT, ['id' => 'desc'], null));
        }

        return view('admin.transactions.index')->with([
            'controller' => 'transaction',
            'action' => 'index',
        ]);
    }

    /**
     * @param Request  $request
     * @param Transaction $transaction
     *
     * @return TransactionResource|View|Factory
     */
    public function show(Request $request, Transaction $transaction)
    {
        //select2
        if ($request->isXmlHttpRequest() && 'select2' == $request->get('query_type')) {
            return new TransactionResource($transaction);
        }

        return view('admin.transactions.show')->with([
            'controller' => 'transaction',
            'action' => 'show',
            'data' => new TransactionResource($transaction),
        ]);
    }
}
