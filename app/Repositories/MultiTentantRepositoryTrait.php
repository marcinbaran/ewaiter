<?php

namespace App\Repositories;

use App\Models\Restaurant;
use Hyn\Tenancy\Contracts\CurrentHostname;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Events\Hostnames\Switched;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Traits\DispatchesEvents;

trait MultiTentantRepositoryTrait
{
    use DispatchesEvents;

    public function reset()
    {
        \DB::reconnect('system');

        app()->instance(CurrentHostname::class, null);
        app(Environment::class)->hostname(null);
        app(Environment::class)->tenant(null);

        $this->emitEvent(new Switched());
    }

    protected function reconnect(Restaurant $restaurant)
    {
        $hostname = Hostname::query()->where('id', $restaurant->hostname_id)->first();

        if (! $hostname instanceof Hostname) {
            throw new \Exception('Hostname not found', 404);
        }
        if (config('database.connections.tenant.database') === $hostname->website->uuid) {
            return;
        }

        config(['database.connections.tenant.database' => $hostname->website->uuid]);
        \DB::reconnect('tenant');

        app()->instance(CurrentHostname::class, $hostname);
        app(Environment::class)->hostname($hostname);
        app(Environment::class)->tenant($hostname->website);

        $this->emitEvent(new Switched($hostname));
    }
}
