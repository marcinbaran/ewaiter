<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CommissionsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    use Exportable;

    protected $commissions;

    public function __construct($commissions)
    {
        $this->commissions = $commissions;
    }

    public function collection()
    {
        return $this->commissions->map(function ($commission) {
            $comment = wordwrap($commission->comment, 50, "\n", true);

            return new Collection([
                __('Id') => $commission->id,
                __('commissions.issued_at') => $commission->issued_at,
                __('commissions.restaurant') => $commission->restaurant_name,
                __('commissions.bill_id') => $commission->bill_id,
                __('commissions.bill_price') => $commission->bill_price,
                __('commissions.commission') => $commission->commission,
                __('commissions.status') => __('commissions.statuses.'.$commission->status),
                __('commissions.comment') => $comment,
            ]);
        });
    }

    public function headings(): array
    {
        return [
            __('Id'),
            __('commissions.issued_at'),
            __('commissions.restaurant'),
            __('commissions.bill_id'),
            __('commissions.bill_price'),
            __('commissions.commission'),
            __('commissions.status'),
            __('commissions.comment'),
        ];
    }
}
