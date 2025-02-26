<?php

namespace App\Console\Commands;

use App\Services\ThumbnailsGenerator;
use Illuminate\Console\Command;

class GenerateThumbnails extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'resource:generate-thumbnails';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->runCommand('croppa:purge', [], $this->getOutput());
        $this->comment('Croppa cache cleared');
        $thumbnailConverter = app(ThumbnailsGenerator::class);

        $glob = glob(storage_path('app/public/tenancy/*/dishes/*/*.*'), GLOB_NOSORT);
        foreach ($glob as $file) {
            try {
                $this->comment('Generating thumbnails for '.$file);
                $thumbnailConverter->generateThumbnailsForFile($file);
            } catch (\Throwable $e) {
                $this->error($e->getMessage());
            }
        }

        return 1;
    }
}
