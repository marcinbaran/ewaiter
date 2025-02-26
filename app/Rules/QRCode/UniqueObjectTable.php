<?php

namespace App\Rules\QRCode;

use App\Models\QRCode;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueObjectTable implements ValidationRule
{
    protected $objectType;

    public function __construct($objectType)
    {
        $this->objectType = $objectType;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $qr = QRCode::where('object_id', $value)
            ->where('object_type', $this->objectType)
            ->first();

        if ($qr) {
            $fail(__('errors.qrcode_already_exists', ['id' => $qr->id]));
        }
    }

    public function message()
    {
        return 'For this object type, only one object ID table is allowed.';
    }
}
