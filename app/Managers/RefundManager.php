<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Managers;

use App\Http\Controllers\ParametersTrait;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefundManager
{
    use ParametersTrait;

    public function __construct()
    {
    }

    /**
     * @param Request $request
     *
     * @return Refund
     */
    public function create(Request $request): Refund
    {
        $params = $this->getParams($request, ['bill_id', 'payment_id', 'amount', 'status'=>Refund::STATUS_REPORTED]);

        $refund = DB::connection('tenant')->transaction(function () use ($params) {
            $refund = Refund::create($params)->fresh();

            return $refund;
        });

        return $refund;
    }

    /**
     * @param Request $request
     * @param Refund   $refund
     *
     * @return Refund
     */
    public function update(Request $request, Refund $refund): Refund
    {
        $params = $this->getParams($request, ['bill_id', 'payment_id', 'amount', 'status']);

        if (! empty($params)) {
            DB::connection('tenant')->transaction(function () use ($params, $refund) {
                $refund->update($params);
                $refund->fresh();
            });
        }

        return $refund;
    }

    /**
     * @param Refund $refund
     *
     * @return Refund
     */
    public function delete(Refund $refund): Refund
    {
        DB::connection('tenant')->transaction(function () use ($refund) {
            $refund->delete();
        });

        return $refund;
    }

    /**
     * @param Refund $refund
     *
     * @return Refund
     */
    public function unlock_refund($refund): Refund
    {
        DB::connection('tenant')->transaction(function () use ($refund) {
            $refund->update([
                'status' => Refund::STATUS_TO_REFUNDED,
            ]);

            return $refund;
        });

        return $refund;
    }
}
