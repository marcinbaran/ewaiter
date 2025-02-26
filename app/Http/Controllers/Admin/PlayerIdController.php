<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\PlayerIdResource;
use App\Models\PlayerId;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;

class PlayerIdController extends Controller
{
    public function __construct()
    {
        PlayerIdResource::wrap('results');
    }

    /** @param Request $request
     *
     * @return View|Factory|AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        if ('select2' == $request->get('query_type')) {
            return new JsonResponse([
                'results' => PlayerId::query()
                    ->select(['id', 'player_id as text'])
                    ->where('player_id', 'like', '%'.$request->get('query_phrase').'%')
                    ->get(['id', 'text'])
                    ->toArray(),
            ]);
        }

        return view('admin.playerIds.index')->with([
                    'controller' => 'playerId',
                    'action' => 'index',
        ]);
    }
}
