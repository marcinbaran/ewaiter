<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Managers;

use App\Http\Controllers\ParametersTrait;
use App\TrWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrWithdrawManager
{
    use ParametersTrait;

    /**
     * @param Request $request
     *
     * @return TrWithdrawal
     */
    public function create(Request $request): TrWithdrawal
    {
        $params = $this->getParams($request, ['name', 'hostname', 'description', 'visibility' => 0, 'provision' => 0, 'account_number']);

        $tr_withdraw = DB::transaction(function () use ($params) {
            $tr_withdraw = TrWithdrawal::create($params)->fresh();

            return $tr_withdraw;
        });

        return $tr_withdraw;
    }

    /**
     * @param Request $request
     * @param TrWithdrawal   $tr_withdraw
     *
     * @return TrWithdrawal
     */
    public function update(Request $request, TrWithdrawal $tr_withdraw): TrWithdrawal
    {
        $params = $this->getParams($request, ['name', 'description', 'visibility' => 0, 'provision' => 0, 'account_number']);
        if (! empty($params)) {
            DB::transaction(function () use ($params, $tr_withdraw) {
                $tr_withdraw->update($params);
                $tr_withdraw->fresh();
            });
        }

        return $tr_withdraw;
    }

    /**
     * @param TrWithdrawal $tr_withdraw
     *
     * @return TrWithdrawal
     */
    public function delete(TrWithdrawal $tr_withdraw): TrWithdrawal
    {
        DB::transaction(function () use ($tr_withdraw) {
            $tr_withdraw->delete();
        });

        return $tr_withdraw;
    }
}
