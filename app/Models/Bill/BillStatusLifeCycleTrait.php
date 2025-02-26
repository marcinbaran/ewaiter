<?php

namespace App\Models\Bill;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait BillStatusLifeCycleTrait
{
    public function getPossibleNextStatuses()
    {
        $isUserAdmin = (bool) count(array_filter(Auth::user()->roles, fn ($role) => $role === User::ROLE_ADMIN));

        switch ($this->status) {
            case self::STATUS_NEW:
                return [
                    self::STATUS_ACCEPTED => __('orders.Accepted'),
                    self::STATUS_CANCELED => __('orders.Canceled'),
                ];
            case self::STATUS_ACCEPTED:
                if ($isUserAdmin) {
                    return [
                        self::STATUS_READY => __('orders.Ready'),
                        self::STATUS_CANCELED => __('orders.Canceled'),
                    ];
                }

                return [
                    self::STATUS_READY => __('orders.Ready'),
                ];
            case self::STATUS_READY:
                return [
                    self::STATUS_RELEASED => __('orders.Released'),
                    self::STATUS_CANCELED => __('orders.Canceled'),
                ];
            case self::STATUS_RELEASED:
                if ($isUserAdmin) {
                    return [
                        self::STATUS_COMPLAINT => __('orders.Complaint'),
                    ];
                }

                return [
                    self::STATUS_RELEASED => __('orders.no-options-to-choose'),
                ];
            case self::STATUS_COMPLAINT:
                return [
                    self::STATUS_COMPLAINT => __('orders.no-options-to-choose'),
                ];
            default:
            case self::STATUS_CANCELED:
                return [
                    self::STATUS_CANCELED => __('orders.no-options-to-choose'),
                ];
        }
    }
}
