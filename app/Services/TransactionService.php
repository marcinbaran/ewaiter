<?php

namespace App\Services;

use App\Events\RefundMobileEvent;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Restaurant;
use App\Models\Settings;
use App\Models\Transaction;
use App\Models\TrWithdrawal;
use Carbon\Carbon;

class TransactionService
{
    public function cronTransferPayments()
    {
        $tenant = app(\Hyn\Tenancy\Environment::class)->tenant();
        if ($tenant) {
            $payments = Payment::where('transferred', 1)->where('paid', 1)->where('type', 'tpay')->where('created_at', '<', Carbon::now()->subHours(2))->get();
            if (count($payments)) {
                $restaurant = Restaurant::where('hostname', $tenant->uuid)->first();
                foreach ($payments as $payment) {
                    $transaction = \DB::transaction(function () use ($payment, $restaurant) {
                        return Transaction::create([
                            'amount' => number_format(($payment->p24_amount / 100), 2, '.', ''),
                            'restaurant_id' => $restaurant->id,
                            'payment_id' => $payment->id,
                            'status' => 1,
                            'visibility' => 1,
                        ]);
                    });
                    if ($transaction->id) {
                        $payment->update(['transferred'=>2]);
                    }
                }
            }
        }
    }

    public function cronRefundPayments()
    {
        $tenant = app(\Hyn\Tenancy\Environment::class)->tenant();
        if ($tenant) {
            $tpayService = new TpayService(
                app('config')['services']['tpay']['login'],
                app('config')['services']['tpay']['pass'],
                app('config')['services']['tpay']['api'],
                app('config')['services']['tpay']['api_pass'],
                app('config')['services']['tpay']['test']
            );
            $refunds = Refund::where('status', Refund::STATUS_TO_REFUNDED)->where('refunded', 0)->get();

            if (count($refunds)) {
                foreach ($refunds as $refund) {
                    $transactionId = $refund->payment->p24_token;
                    $amount = number_format(($refund->payment->p24_amount / 100), 2, '.', '');
                    if ($transactionId && $amount) {
                        $responseService = $tpayService->refund($transactionId, $amount);
                        if (isset($responseService['result']) && 1 == $responseService['result']) {
                            $refund->update(['refunded'=>true, 'status'=>Refund::STATUS_REFUNDED]);
                            $refund->payment->update(['paid'=>Payment::PAID_REFUNDED]);

                            event(new RefundMobileEvent($refund->fresh()));
                        } else {
                            $refund->update(['status'=>Refund::STATUS_ERROR]);
                            $restaurant_email = Settings::getSetting('kontakt', 'email', true, false);
                            if ($restaurant_email) {
                                \Mail::to($restaurant_email)->send(new \App\Mail\RefundErrorMail($refund, json_encode($responseService)));
                            }
                            \Mail::to(app('config')['app']['dev_mail'])->send(new \App\Mail\RefundErrorMail($refund, json_encode($responseService)));
                            \Mail::to('jaroslaw.konsur@zetorzeszow.pl')->send(new \App\Mail\RefundErrorMail($refund, json_encode($responseService)));
                        }
                    }
                }
            }
        }
    }

    public function cronWithdraw()
    {
        $tenant = app(\Hyn\Tenancy\Environment::class)->tenant();
        if (! $tenant) {
            $tpayService = new TpayService(
                app('config')['services']['tpay']['login'],
                app('config')['services']['tpay']['pass'],
                app('config')['services']['tpay']['api'],
                app('config')['services']['tpay']['api_pass'],
                app('config')['services']['tpay']['test']
            );
            $active_restaurants = Restaurant::
                whereHas('transactions', function ($query) {
                    $query->where('transactions.withdrawal', 0)
                        ->where('transactions.type', 'tpay')
                        ->where('transactions.visibility', 1)
                        ->where('transactions.status', 1);
                })
                ->where('visibility', 1)->whereNotNull('account_number')->get();
            if (count($active_restaurants)) {
                \DB::connection('system')->beginTransaction();
                $csv = '';
                $w_ids = [];
                foreach ($active_restaurants as $restaurant) {
                    $transactions = Transaction::where('restaurant_id', $restaurant->id)->where('withdrawal', 0)->where('visibility', 1)->where('status', 1)->get();
                    if (count($transactions)) {
                        $amount = null;
                        $withdrawal = TrWithdrawal::create([
                            'restaurant_id' => $restaurant->id,
                            'account_number' => $restaurant->account_number,
                            'status' => 0,
                            'visibility' => 1,
                        ]);
                        foreach ($transactions as $transaction) {
                            $amount += $transaction->amount;
                            $transaction->update([
                                'tw_id' => $withdrawal->id,
                                'withdrawal' => 1,
                                'status' => 2,
                            ]);
                        }
                        if ($amount) {
                            $amount = number_format($amount, 2, '.', '');
                            $restaurant_name = $restaurant->name;
                            $street = isset($restaurant->address->street) ? $restaurant->address->street.' '.$restaurant->address->building_number : '';
                            $city = isset($restaurant->address->postcode) ? $restaurant->address->postcode.' '.$restaurant->address->city : '';

                            $account_number = $restaurant->account_number;
                            $receiver_1 = substr($restaurant_name, 0, 35);
                            $receiver_2 = substr($street, 0, 35);
                            $receiver_3 = substr($city, 0, 35);
                            $receiver_4 = '';
                            $total_amount = $amount;
                            $title_1 = 'Wyciąg ID: '.$withdrawal->id;
                            $title_2 = 'Data: '.date('Y-m-d H:i:s');
                            $csv .= $account_number.';'.$receiver_1.';'.$receiver_2.';'.$receiver_3.';'.$receiver_4.';'.$total_amount.';'.$title_1.';'.$title_2."\n";
                            $w_ids[] = [
                                'amount' => $amount,
                                'id' => $withdrawal->id,
                            ];
                        }
                    }
                }
                if ($csv != '') {
                    $massPaymentCreate = $tpayService->massPaymentCreate($csv);
                    if (isset($massPaymentCreate['pack_id']) && $massPaymentCreate['pack_id']) {
                        $pack_id = $massPaymentCreate['pack_id'];
                        $authorize = $tpayService->massPaymentAuthorize($pack_id);
                        if (isset($authorize['result']) && $authorize['result'] == 1) {
                            foreach ($w_ids as $w_id) {
                                TrWithdrawal::where('id', $w_id['id'])->update(['amount' => $w_id['amount'], 'status' => 1, 'pack_id'=>$pack_id]);
                            }
                            \DB::connection('system')->commit();
                        } else {
                            \Mail::to(app('config')['app']['dev_mail'])->send(new \App\Mail\WithdrawErrorMail(json_encode($w_ids), json_encode($authorize)));
                            \Mail::to('jaroslaw.konsur@zetorzeszow.pl')->send(new \App\Mail\WithdrawErrorMail(json_encode($w_ids), json_encode($authorize)));
                        }
                    } else {
                        \Mail::to(app('config')['app']['dev_mail'])->send(new \App\Mail\WithdrawErrorMail(json_encode($w_ids), json_encode($massPaymentCreate)));
                        \Mail::to('jaroslaw.konsur@zetorzeszow.pl')->send(new \App\Mail\WithdrawErrorMail(json_encode($w_ids), json_encode($massPaymentCreate)));
                    }
                }
            }
        }
    }
}
