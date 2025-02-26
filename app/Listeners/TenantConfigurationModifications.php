<?php

namespace App\Listeners;

use Hyn\Tenancy\Events\Database\ConfigurationLoaded;

class TenantConfigurationModifications
{
    public function handle(ConfigurationLoaded $event)
    {
        $host = app('request')->server()['HTTP_HOST'] ?? '';
        if ($host == 'localhost:8002') {
            // $event->configuration['host'] = $event->configuration['uuid'];
        }
    }
}
