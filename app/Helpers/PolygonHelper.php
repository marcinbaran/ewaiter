<?php

namespace App\Helpers;

class PolygonHelper
{
    const string LATITUDE_KEY = 'lat';
    const string LONGITUDE_KEY = 'lng';
    const int DEFAULT_COUNT_POLYGON_POINT = 36;
    const int EARTH_RADIUS_IN_KM = 6371;
    const int DEGREES_OF_CIRCLE = 360;

    public static function getCirclePolygonCoordinates(
        float $latitude,
        float $longitude,
        int   $radius,
        int   $points = self::DEFAULT_COUNT_POLYGON_POINT
    ): array
    {
        $coordinates = [];
        $earthRadius = self::EARTH_RADIUS_IN_KM;

        $angularDistance = $radius / $earthRadius;

        $lat = deg2rad($latitude);
        $lon = deg2rad($longitude);

        return self::calculatePolygonCoordinates($lat, $angularDistance, $lon, $coordinates, $points);
    }

    private static function calculatePolygonCoordinates(
        float     $lat,
        float|int $angularDistance,
        float     $lon,
        array     $coordinates,
        int       $points
    ): array
    {
        for ($i = 0; $i <= self::DEGREES_OF_CIRCLE; $i += self::DEGREES_OF_CIRCLE / $points) {
            $bearing = deg2rad($i);

            $latCalculated = self::calculateLat($lat, $angularDistance, $bearing);
            $lonCalculated = self::calculateLon($lon, $bearing, $angularDistance, $lat, $latCalculated);

            $newLat = rad2deg($latCalculated);
            $newLon = rad2deg($lonCalculated);

            $coordinates[] = [$newLat, $newLon];
        }

        return $coordinates;
    }

    public static function calculateLat(float $lat, float|int $angularDistance, float $bearing): float
    {
        return asin(sin($lat) * cos($angularDistance) + cos($lat) * sin($angularDistance) * cos($bearing));
    }

    public static function calculateLon(
        float     $lon,
        float     $bearing,
        float|int $angularDistance,
        float     $lat,
        float     $newLat
    ): float
    {
        return $lon + atan2(sin($bearing) * sin($angularDistance) * cos($lat), cos($angularDistance) - sin($lat) * sin($newLat));
    }

    public static function isPolygonInside(array $innerPolygon, array $outerPolygon)
    {
        foreach ($innerPolygon as $point) {
            if (!self::isPointInPolygon(['lat' => $point[0], 'lng' => $point[1]], $outerPolygon)) {
                return false;
            }
        }

        $n = count($innerPolygon);
        $m = count($outerPolygon);

        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $m; $j++) {
                if (self::doEdgesIntersect(
                    $innerPolygon[$i],
                    $innerPolygon[($i + 1) % $n],
                    $outerPolygon[$j],
                    $outerPolygon[($j + 1) % $m]
                )) {
                    return false;
                }
            }
        }

        return true;
    }

    public static function isPointInPolygon(array $point, array $polygon): bool
    {
        $x = $point[self::LATITUDE_KEY];
        $y = $point[self::LONGITUDE_KEY];

        return self::calculatePointInPolygon($polygon, $y, $x);
    }

    private static function calculatePointInPolygon(array $polygon, float $y, float $x): bool
    {
        $inside = false;

        for ($i = 0, $j = count($polygon) - 1; $i < count($polygon); $j = $i++) {
            $xi = $polygon[$i][0];
            $yi = $polygon[$i][1];
            $xj = $polygon[$j][0];
            $yj = $polygon[$j][1];

            $intersect = (($yi > $y) != ($yj > $y)) &&
                ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);
            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }

    public static function doEdgesIntersect(array $a, array $b, array $c, array $d)
    {
        $det = function ($a, $b, $c, $d) {
            return ($c[0] - $a[0]) * ($d[1] - $b[1]) - ($c[1] - $a[1]) * ($d[0] - $b[0]);
        };

        $det1 = $det($a, $b, $c, $d);
        $det2 = $det($a, $b, $d, $c);
        $det3 = $det($c, $d, $a, $b);
        $det4 = $det($c, $d, $b, $a);

        return ($det1 * $det2 < 0) && ($det3 * $det4 < 0);
    }
}


