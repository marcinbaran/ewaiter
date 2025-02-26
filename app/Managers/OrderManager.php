<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Managers;

use App\Events\OrderStatusEvent;
use App\Http\Controllers\ParametersTrait;
use App\Models\Order;
use App\Models\User;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderManager
{
    use ParametersTrait;

    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @param TranslationService $service
     */
    public function __construct(TranslationService $service)
    {
        $this->transService = $service;
    }

    /**
     * @param Request $request
     *
     * @return Order
     */
    public function create(Request $request): Order
    {
        $params = $this->getParams($request, ['quantity', 'price', 'tax', 'discount', 'additions']);
        $references = $this->getParams($request, ['dish', 'bill', 'table']);
        $user = Auth::user();
        $params['dish_id'] = $references['dish']['id'];
        ! isset($references['bill']['id']) ?: $params['bill_id'] = $references['bill']['id'];
        $params['table_id'] = $references['table']['id'] ?? $user->hasRoles([User::ROLE_TABLE, User::ROLE_USER]) ? $user->table->id : null;
        $order = DB::connection('tenant')->transaction(function () use ($params) {
            $order = Order::create($params)->fresh();

            return $order;
        });

        event(new OrderStatusEvent($order->fresh(), $this->transService));

        return $order;
    }

    /**
     * @param Request $request
     * @param Order   $order
     *
     * @return Order
     */
    public function update(Request $request, Order $order): Order
    {
        $params = $this->getParams($request, ['quantity', 'price', 'tax', 'discount', 'status', 'paid', 'additions']);
        $references = $this->getParams($request, ['dish', 'bill', 'table']);

        ! isset($references['bill']['id']) ?: $params['bill_id'] = $references['bill']['id'];
        ! isset($references['table']['id']) ?: $params['table_id'] = $references['table']['id'];
        ! isset($references['dish']['id']) ?: $params['dish_id'] = $references['dish']['id'];

        if (! empty($params)) {
            DB::connection('tenant')->transaction(function () use ($params, $order) {
                if ($params['status'] == Order::STATUS_ACCEPTED && $params['status'] != $order->status) {
                    $params['accepted_at'] = date('Y-m-d H:i:s');
                }
                if ($params['status'] == Order::STATUS_RELEASED && $params['status'] != $order->status) {
                    $params['released_at'] = date('Y-m-d H:i:s');
                }
                $order->update($params);
                $order->fresh();
            });
        }

        event(new OrderStatusEvent($order->fresh(), $this->transService));

        return $order;
    }

    /**
     * @param Order $order
     *
     * @return Order
     */
    public function delete(Order $order): Order
    {
        DB::connection('tenant')->transaction(function () use ($order) {
            $order->delete();
        });

        return $order;
    }

    /**
     * @param Request $request
     *
     * @return Order
     */
    public function status_edit($request): Order
    {
        $order = Order::where('id', $request->get('pk'))->first();
        DB::connection('tenant')->transaction(function () use ($order, $request) {
            $order->status = $request->get('value');
            $order->save();
        });

        event(new OrderStatusEvent($order->fresh(), $this->transService));

        return $order;
    }
}
