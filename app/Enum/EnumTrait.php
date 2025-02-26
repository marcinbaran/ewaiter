<?php

namespace App\Enum;

trait EnumTrait
{
    public static function getValues(): array
    {
        $values = [];

        foreach (self::cases() as $status) {
            $values[] = $status->value;
        }

        return $values;
    }

    public static function getKeys(): array
    {
        $keys = [];

        foreach (self::cases() as $status) {
            $keys[] = $status->name;
        }

        return $keys;
    }

    public static function getKeyValuePairs(): array
    {
        $cases = [];

        foreach (self::cases() as $case) {
            $cases[$case->name] = $case->value;
        }

        return $cases;
    }

    public static function getValuesForRequestRule(): string
    {
        $valuesForRequestRule = '';

        foreach (self::getValues() as $value) {
            $valuesForRequestRule .= $value.',';
        }

        return rtrim($valuesForRequestRule, ',');
    }
}
