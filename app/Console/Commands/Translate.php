<?php

namespace App\Console\Commands;

use App\Repositories\MultiTentantRepositoryTrait;
use App\Services\TranslatorService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

class Translate extends Command
{

    use MultiTentantRepositoryTrait;

    protected $signature = 'app:translate {hostname? : The hostname of restaurant.} {model? : The model of resource.} {target? : The target language.} {source? : The source language.} {--force : Force translation}';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $hostname = $this->argument('hostname');
        $model    = $this->argument('model');
        $target   = $this->argument('target');
        $source   = $this->argument('source');
        $force    = $this->option('force');

        $restaurant = app('App\Models\Restaurant')->where('hostname', $hostname)->first();

        if (!$restaurant) {
            $this->error('Restaurant not found');

            return;
        }

        $this->reconnect($restaurant);

        $modelClassname = 'App\\Models\\' . ucfirst($model);
        $model          = new $modelClassname();
        if (!$model || !$model instanceof Model) {
            $this->error('Model not found');
        }

        $this->info('Translating...');

        $translator = app(TranslatorService::class);

        foreach ($model->all() as $item) {
            foreach ($model->translatable as $translatable) {
                $targetText = $item->getTranslation($translatable, $target);
                if ($targetText && !$force) {
                    $this->info('Skipping: ' . $item->getTranslation($translatable, $target));
                    continue;
                }

                $sourceText = $item->getTranslation($translatable, $source);
                $item->setTranslation($translatable, $target, $translator->translate($sourceText, $source, $target));
                $item->save();
            }
        }

        $this->info('Translation complete');
    }
}
