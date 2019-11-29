<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Repositories\Contracts\SubscriptionRepository;
use App\Models\Subscription;

class TrialCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trial:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trial period check';

    protected $subscriptionRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        parent::__construct();

        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        $this->subscriptionRepository->where([
            'status' => Subscription::STATUS_TRIALING,

        ])->where([
            ['trial_ends_at', null],
            ['trial_ends_at', '<=', $now, 'or'],
        ])
        ->update([
            'status' => Subscription::STATUS_CANCELLED,
        ]);
    }
}
