<?php

namespace App\Managers;

use App\Enum\Voucher\VoucherAddingType;
use App\Http\Controllers\ParametersTrait;
use App\Repositories\VoucherRepository;
use Illuminate\Support\Facades\DB;

class VoucherManager
{
    use ParametersTrait;

    private VoucherRepository $voucherRepository;

    public function __construct()
    {
        $this->voucherRepository = new VoucherRepository();
    }

    public function store($request)
    {
        $params = $this->getParams($request, [
            'adding_type',
            'quantity',
            'comment',
            'value',
        ]);

        $adding_type = VoucherAddingType::from($params['adding_type']);

        if ($adding_type === VoucherAddingType::MULTIPLE) {
            $vouchers = $this->voucherRepository->generateVouchers($params['comment'], $params['value'], $params['quantity']);
        } else {
            $vouchers = [$this->voucherRepository->generateVoucher($params['comment'], $params['value'])];
        }

        return DB::transaction(function () use ($vouchers) {
            foreach ($vouchers as $voucher) {
                $voucher->save();
            }
        });
    }

    public function update($voucher, $request)
    {
        $params = $this->getParams($request, [
            'comment',
            'value',
        ]);

        $voucher->comment = $params['comment'];
        $voucher->value = $params['value'];

        return $voucher->save();
    }

    public function delete($voucher)
    {
        return $voucher->delete();
    }
}
