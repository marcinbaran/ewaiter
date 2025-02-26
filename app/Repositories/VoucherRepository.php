<?php

namespace App\Repositories;

use App\Models\Voucher;

class VoucherRepository
{
    protected $model = Voucher::class;

    public function generateVoucher(string $comment, float $value): Voucher
    {
        $voucher = new Voucher();
        $voucher->code = self::generateCode();
        while ($this->model::where('code', $voucher->code)->exists()) {
            $voucher->code = self::generateCode();
        }
        $voucher->comment = $comment;
        $voucher->value = $value;

        return $voucher;
    }

    public function generateVouchers(string $comment, float $value, int $quantity): array
    {
        $vouchers = [];

        for ($i = 0; $i < $quantity; $i++) {
            $vouchers[] = $this->generateVoucher($comment.' #'.$i + 1, $value);
        }

        return $vouchers;
    }

    private function generateCode(string $mask = null, string $characters = null): string
    {
        $mask = $mask ?: config('vouchers.mask', '****-****-****-****');
        $characters = $characters ?: config('vouchers.characters', '23456789ABCDEFGHJKLMNPQRSTUVWXYZ');
        $prefix = config('vouchers.prefix', '');
        $suffix = config('vouchers.suffix', '');
        $separator = config('vouchers.separator', '-');

        $code = preg_replace_callback('/\*/', function (array $matches) use ($characters) {
            try {
                return $characters[random_int(0, mb_strlen($characters) - 1)];
            } catch (\Exception $e) {
                return $characters[0];
            }
        }, $mask);

        return $this->wrapCode($code, $separator, $prefix, $suffix);
    }

    private function wrapCode(string $str, string $separator = '-', string $prefix = '', string $suffix = ''): string
    {
        $prefix = ! empty($prefix) ? $prefix.$separator : '';
        $suffix = ! empty($suffix) ? $separator.$suffix : '';

        return $prefix.$str.$suffix;
    }
}
