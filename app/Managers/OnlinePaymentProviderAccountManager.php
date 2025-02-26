<?php

namespace App\Managers;

use App\Http\Controllers\ParametersTrait;
use App\Http\Requests\Admin\OnlinePaymentProviderAccountRequest;
use App\Models\OnlinePaymentProviderAccount;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class OnlinePaymentProviderAccountManager
{
    use ParametersTrait;

    public function __construct()
    {
    }

    public function store(OnlinePaymentProviderAccountRequest $request): OnlinePaymentProviderAccount
    {
        $params = $this->getParams($request, [
            'comment',
            'login',
            'password',
            'api_key',
            'api_password',
            'restaurant_id',
        ]);

        return DB::transaction(function () use ($params) {
            $account = OnlinePaymentProviderAccount::where('restaurant_id', $params['restaurant_id'])->first();

            if ($account) {
                $account->update([
                    'comment' => $params['comment'],
                    'login' => Crypt::encryptString($params['login']),
                    'password' => Crypt::encryptString($params['password']),
                    'api_key' => Crypt::encryptString($params['api_key']),
                    'api_password' => Crypt::encryptString($params['api_password']),
                ]);
            } else {
                $account = OnlinePaymentProviderAccount::create([
                    'comment' => $params['comment'],
                    'login' => Crypt::encryptString($params['login']),
                    'password' => Crypt::encryptString($params['password']),
                    'api_key' => Crypt::encryptString($params['api_key']),
                    'api_password' => Crypt::encryptString($params['api_password']),
                    'restaurant_id' => $params['restaurant_id'],
                ]);
            }

            return $account;
        });
    }

    public function delete(OnlinePaymentProviderAccount $onlinePaymentProviderAccount): bool
    {
        return $onlinePaymentProviderAccount->delete();
    }

    public function show(int $restaurantId)
    {

        $account = OnlinePaymentProviderAccount::where('restaurant_id', $restaurantId)
            ->latest('created_at')
            ->first();

        if (!$account) {
            return [
                'comment' => '',
                'login' => '',
                'password' => '',
                'api_key' => '',
                'api_password' => '',
            ];
        }

        return [
            'comment' => $account->comment,
            'login' => Crypt::decryptString($account->login),
            'password' => Crypt::decryptString($account->password),
            'api_key' => Crypt::decryptString($account->api_key),
            'api_password' => Crypt::decryptString($account->api_password),
        ];
    }
}
