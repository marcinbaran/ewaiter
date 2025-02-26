<?php

namespace App\Console\Commands;

use App\Services\SerwerSMSService;
use Illuminate\Console\Command;

class TestSmsCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'test:sms {targetPhoneNumber}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for testing password reset SMS';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /** @var SerwerSMSService $serverSms */
        $serverSms = app(SerwerSMSService::class);
        $res = $serverSms->sendResetPasswordCodeSMS($this->argument('targetPhoneNumber'), '1234');

        dd($res);
    }
}
