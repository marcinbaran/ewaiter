<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Managers;

use App\Http\Controllers\ParametersTrait;
use App\Models\Payment;
use App\Models\Settings;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentManager
{
    use ParametersTrait;

    /**
     * @param Request $request
     *
     * @return Payment
     */
    public function create(Request $request): Payment
    {
        $params = $this->getParams($request, ['quantity', 'price', 'tax', 'discount', 'additions']);
        $references = $this->getParams($request, ['dish', 'bill', 'table']);
        $user = Auth::user();
        $params['dish_id'] = $references['dish']['id'];
        ! isset($references['bill']['id']) ?: $params['bill_id'] = $references['bill']['id'];
        $params['table_id'] = $references['table']['id'] ?? $user->hasRoles([User::ROLE_TABLE, User::ROLE_USER]) ? $user->table->id : null;

        $order = DB::connection('tenant')->transaction(function () use ($params) {
            $order = Payment::create($params)->fresh();

            return $order;
        });

        return $order;
    }

    /**
     * @param Request $request
     * @param Payment   $order
     *
     * @return Payment
     */
    public function update(Request $request, Payment $order): Payment
    {
        $params = $this->getParams($request, ['quantity', 'price', 'tax', 'discount', 'status', 'paid', 'additions']);
        $references = $this->getParams($request, ['dish', 'bill', 'table']);

        ! isset($references['bill']['id']) ?: $params['bill_id'] = $references['bill']['id'];
        ! isset($references['table']['id']) ?: $params['table_id'] = $references['table']['id'];
        ! isset($references['dish']['id']) ?: $params['dish_id'] = $references['dish']['id'];

        if (! empty($params)) {
            DB::connection('tenant')->transaction(function () use ($params, $order) {
                $order->update($params);
                $order->fresh();
            });
        }

        return $order;
    }

    /**
     * @param Payment $order
     *
     * @return Payment
     */
    public function delete(Payment $order): Payment
    {
        DB::connection('tenant')->transaction(function () use ($order) {
            $order->delete();
        });

        return $order;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function checkIfAvailable(string $type): bool
    {
        return (bool) Settings::getSetting('sposoby_platnosci', $type, true, false, true);
    }

    /*
     * T-pay expects integer value as a transaction amount.
     * This means that 18.99 zł should be represented as 1899
     * */
    public function getTpayTransactionAmount($amount): int
    {
        return (int) round($amount * 100);
    }
}
