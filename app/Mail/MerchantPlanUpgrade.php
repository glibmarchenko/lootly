<?php

namespace App\Mail;

use App\Models\Plan;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MerchantPlanUpgrade extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public $plan;

    public $features;

    /**
     * Create a new message instance.
     *
     * @param \App\User        $user
     * @param \App\Models\Plan $plan
     *
     * @internal param bool $createdAutomatically
     * @internal param string $password
     */
    public function __construct(User $user, Plan $plan)
    {
        $this->user = $user;
        $this->plan = $plan;
        $this->features = [];

        switch ($this->plan->type) {
            case 'growth':
                $this->features = [
                    [
                        'url'   => 'http://support.lootly.io/referrals/referral-program-basics',
                        'title' => 'Referral Program',
                    ],
                    [
                        'url'   => 'http://support.lootly.io/design-customization/email-customization',
                        'title' => 'Email Customization',
                    ],
                    [
                        'url'   => 'http://support.lootly.io/manage-customers/customer-segmentation',
                        'title' => 'Customer Segmentation',
                    ],
                ];
                break;
            case 'ultimate':
                $this->features = [
                    [
                        'url'   => 'http://support.lootly.io/vip/vip-program-basics',
                        'title' => 'VIP Program',
                    ],
                    [
                        'url'   => 'http://support.lootly.io/design-customization/advanced-design-customization',
                        'title' => 'Advanced Customization',
                    ],
                    [
                        'url'   => 'http://support.lootly.io/reports/introduction-to-insights-reports',
                        'title' => 'Insights & Reports',
                    ],
                ];
                break;
            case 'enterprise':
                $this->features = [
                    [
                        'url'   => 'http://support.lootly.io/getting-started/custom-sender-domain',
                        'title' => 'Custom Sender Domain',
                    ],
                    [
                        'url'   => 'http://support.lootly.io/integrations/api-access',
                        'title' => 'API Access',
                    ],
                    [
                        'url'   => '',
                        'title' => 'Dedicated Account Manager',
                    ],
                ];
                break;
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Lootly Plan Upgrade')->view('emails.merchants.plan-upgrade');
    }
}
