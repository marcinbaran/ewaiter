<?php

namespace App\Console\Commands;

use App\Models\FireBaseNotificationV2;
use App\Repositories\MultiTentantRepositoryTrait;
use App\Services\FirebaseServiceV2;
use Illuminate\Console\Command;

class TestPushMessageCommand extends Command
{
    use MultiTentantRepositoryTrait;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'test:push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for testing push messages';

    public function __construct(private readonly FirebaseServiceV2 $firebaseService)
    {
        parent::__construct();
    }

    public function handle()
    {
        FireBaseNotificationV2::create([
            'user_id' => 97,
            'title' => __('firebase.E-waiter'),
            'body' => __('Canceled automatically'),
            'data' => json_encode([
                'title' => __('firebase.E-waiter'),
                'body' => __('Canceled automatically'),
                'url' => 'bills',
                'id' => '$bill->id',
            ]),
        ]);
    }
}
