<?php

declare(strict_types=1);

namespace App\Collections;

use App\Models\Settings;
use stdClass;

class SettingsCollection extends stdClass
{
    public function __construct()
    {
        $settings = Settings::all()->toArray();
        foreach ($settings as $setting) {
            $this->{$setting['key']} = $setting;
        }
    }

    public function getItem(string $settingName, string $attribute): int|float|string
    {
        return $this->{$settingName}['value'][$attribute];
    }

    public function getItems(string $settingName): array
    {
        return $this->{$settingName}['key'];
    }
}
