<?php

namespace App\Decorators;

use App\Enum\Commission\CommissionStatus;
use App\Models\Commission;

class CommissionStatusDecorator
{
    public function decorate(Commission $commission)
    {
        $classes = '';
        $styles = '';
        $text = '';
        $status = CommissionStatus::from($commission->status);
        switch ($status) {
            case CommissionStatus::ACTIVE:
                $styles = 'background-color: var(--green-200); color: var(--gray-900)';
                $text = __('commissions.statuses.active');
                break;
            case CommissionStatus::FINISHED:
                $styles = 'background-color: var(--blue-200); color: var(--gray-900)';
                $text = __('commissions.statuses.finished');
                break;
            default:
            case CommissionStatus::CANCELED:
                $styles = 'background-color: var(--red-200); color: var(--gray-900)';
                $text = __('commissions.statuses.canceled');
                break;
        }

        return view('admin.partials.decorators.commission-status', ['classes' => $classes, 'styles' => $styles, 'text' => $text]);
    }
}
