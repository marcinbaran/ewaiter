<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Repositories\MultiTentantRepositoryTrait;
use App\Services\SerwerSMSService;
use Illuminate\Console\Command;

class TestPhoneCallCommand extends Command
{
    use MultiTentantRepositoryTrait;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'test:phone-call {targetPhoneNumber} {restaurantId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for testing phone calls';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->reconnect(Restaurant::find($this->argument('restaurantId'))->first());

        /** @var SerwerSMSService $serverSms */
        $serverSms = app(SerwerSMSService::class);
        $serverSms->setTargetPhoneNumber($this->argument('targetPhoneNumber'));
        $serverSms->setText('Test, raz, dwa, trzy. To jest testowe połączenie telefoniczne.');
        $serverSms->makeTestSMS();

        $this->info($serverSms->cronCall());
    }
}
