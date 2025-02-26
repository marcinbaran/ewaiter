<?php

namespace App\Console\Commands;

use App\Models\Resource;
use App\Models\Restaurant;
use App\Repositories\MultiTentantRepositoryTrait;
use App\Services\UploadService;
use Illuminate\Console\Command;

class RemoveOrphantResourcesCommand extends Command
{
    use MultiTentantRepositoryTrait;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'resource:remove-orphant';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (Restaurant::all() as $restaurant) {
            $this->info('Restaurant '.$restaurant->name);

            $this->reconnect($restaurant);

            $orphants = \DB::connection('tenant')->table('resources')
                ->whereRaw('LENGTH(resourcetable_id) = 10')
                ->where('created_at', '<', now()->subHours(config('upload.orphants_ttl'))->format('Y-m-d H:i:s'))
                ->get();

            $this->info('Found '.$orphants->count().' orphants');

            foreach ($orphants as $orphant) {
                $resource = new Resource();
                $resource->resourcetable_id = $orphant->resourcetable_id;
                $resource->resourcetable_type = $orphant->resourcetable_type;
                $resource->filename = $orphant->filename;
                (new UploadService())->removeResource($resource);
                \DB::connection('tenant')->table('resources')->where('id', $orphant->id)->delete();
                $this->info('Resource '.$orphant->resourcetable_id.' deleted');
            }
        }

        return 1;
    }
}
