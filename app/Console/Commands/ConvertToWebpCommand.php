<?php

namespace App\Console\Commands;

use App\Services\WebpConverter;
use Illuminate\Console\Command;

class ConvertToWebpCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'resource:convert-webp';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $converter = app(WebpConverter::class);

        try {
            $converter->convertAllResourcesToWebp();
        } catch(\Throwable $e) {
            $this->error($e->getMessage());
        }

        return 1;
    }
}
