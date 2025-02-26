<?php

namespace App\Console\Commands;

use App\Actions\Assistant\ExportAction;
use App\Models\Restaurant;
use App\Repositories\MultiTentantRepositoryTrait;
use Illuminate\Console\Command;

class ExportForAssistantCommand extends Command
{
    use MultiTentantRepositoryTrait;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'assistant:export';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $exportAction = new ExportAction();
//        $json = $exportAction->exportAllRestaurants();

        $dirname = storage_path('app/assistant-export/'.date('Ymd-h-i-s'));
        mkdir($dirname, 0777, true);

        foreach (Restaurant::query()->where('visibility', 1)->get() as $restaurant) {
            $arr = $exportAction->exportRestaurant($restaurant);
            file_put_contents($dirname.'/'.$restaurant->name.'.json', json_encode($arr, JSON_PRETTY_PRINT));
        }
    }
}
