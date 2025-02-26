<?php

namespace App\Rules\OnlinePaymentProviderAccount;

use App\Services\TpayService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckCredential implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->checkCredential()) {
            $fail(__('admin.The user credentials were incorrect'));
        }
    }

    private function checkCredential(): bool
    {
        $tPayService = new TpayService(
            (int) request()->input('login'),
            request()->input('password'),
            request()->input('api_key'),
            request()->input('api_password')
        );

        return $tPayService->testCredentials();
    }
}
