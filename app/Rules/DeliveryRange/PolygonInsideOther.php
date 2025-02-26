<?php

namespace App\Rules\DeliveryRange;

use App\Helpers\PolygonHelper;
use App\Models\DeliveryRange;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;


class PolygonInsideOther implements ValidationRule
{

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $polygon = json_decode($value);
        $delivery_ranges = DeliveryRange::all();
        $coordinates = $delivery_ranges->pluck('range_polygon');
        if (count($coordinates) > 0) {
            foreach ($coordinates as $coordinate) {
                if ($coordinate !== null) {
                    $coordinate = json_decode($coordinate);
                    if (!PolygonHelper::isPolygonInside($coordinate, $polygon)) {
                        $fail(__('validation.delivery_range.polygon_inside_other'));
                    }
                }
            }
        }

    }


}
