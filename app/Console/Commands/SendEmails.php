<?php

namespace App\Console\Commands;

use App\Mail\TestMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $emails = [
            'jchodzinski@primebitstudio.com',
        ];
        $this->info('Sending emails...');
        foreach ($emails as $email) {
            Mail::to($email)->send(new TestMail());
        }
        $this->info('Emails sent!');
    }
}
