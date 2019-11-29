<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Repositories\CustomerRepository;
use App\Repositories\MerchantRepository;
use App\Repositories\TierRepository;
use App\Repositories\TierSettingsRepository;
use App\Repositories\Contracts\TierHistoryRepository;
use App\Helpers\CustomerService;
use App\Models\TierHistory;
use Illuminate\Support\Facades\Log;

class CustomerTier extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh:tiers';

    private $customerRepository;

    private $merchantRepository;

    private $tierRepository;

    private $tierSettingsRepository;

    private $tierHistory;

    private $customerService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        CustomerRepository $customerRepository,
        MerchantRepository $merchantRepository,
        TierRepository $tierRepository,
        TierSettingsRepository $tierSettingsRepository,
        TierHistoryRepository $tierHistory,
        CustomerService $customerService
    ) {
        parent::__construct();

        $this->customerRepository = $customerRepository;
        $this->merchantRepository = $merchantRepository;
        $this->tierRepository = $tierRepository;
        $this->tierSettingsRepository = $tierSettingsRepository;
        $this->tierHistory = $tierHistory;
        $this->customerService = $customerService;
    }

    public function handle()
    {
        $this->checkRollingDays();
    }

    public function checkRollingDays()
    {
        $customers = $this->customerRepository->getCustomerByDate();

        $tiers = [];

        foreach ($customers as $customer) {
            if (! isset($tiers[$customer->merchant_id])) {
                $tiers[$customer->merchant_id] = $this->tierRepository->get($customer->merchant);
            }

            $tierSettings = $this->tierSettingsRepository->getTierSettingsByMerchantId($customer->merchant_id);
            $isActivityUserChanges = false;

            if ($tierSettings && $tierSettings->isProgramStatus()) {
                $rollingPeriodType = $tierSettings->getRollingPeriodType();
                $rollingPeriodNum = $tierSettings->getRollingPeriodNumber();

                if ($rollingPeriodType && $rollingPeriodNum) {
                    $isActivityUserChanges = $this->customerService->isActivityUserChanges($customer, $rollingPeriodType, $rollingPeriodNum);
                }
            }

            foreach ($tiers[$customer->merchant_id] as $tier) {
                $rolling_days = $tier->rolling_days;

                $date = $carbon = new Carbon('-' . $rolling_days . ' days');

                if (strtotime($customer->created_at) > strtotime($date)
                    && $customer->points > $tier->spend_value
                    && ! $isActivityUserChanges) {
                    // Add record to customer's tier history
                    $this->tierHistory->create([
                        'customer_id' => $customer->id,
                        'new_tier_id' => $tier->id,
                        'old_tier_id' => $customer->tier_id,
                        'activity' => TierHistory::ACTIVITY_UPDATE,
                    ]);

                    $this->customerRepository->updateTier($customer->id, $tier->id);
                }
            }
        }
    }
}
