<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeHash extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'make:hash {pwd? : The name of the migration.}';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! $this->input->getArgument('pwd')) {
            $this->error('Please provide a password to hash');
            return;
        }

        $this->info('Hashed password: ' . Hash::make($this->input->getArgument('pwd')));
    }
}
