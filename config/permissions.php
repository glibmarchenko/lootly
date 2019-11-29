<?php

return [
    /**
     * Paid features typecodes
     */
    'typecode' => [
        'ReadContent'                   => 'ReadContent',
        'TrustSpotReview'               => 'TrustSpotReview',
        'EmailCustomization'            => 'EmailCustomization',
        'ReferralProgram'               => 'ReferralProgram',
        'ImportExistingCustomers'       => 'ImportExistingCustomers',
        'Integrations'                  => 'Integrations',
        'RewardsLink'                   => 'RewardsLink',
        'CustomerSegmentation'          => 'CustomerSegmentation',
        'EarningLimits'                 => 'EarningLimits',
        'SpendingLimits'                => 'SpendingLimits',
        'EmployeeAccess'                => 'EmployeeAccess',
        'RemoveLootlyBranding'          => 'RemoveLootlyBranding',
        'RemoveLootlyBrandingEmail'     => 'RemoveLootlyBrandingEmail',
        'EmailEarningCustomization'     => 'EmailEarningCustomization',
        'EmailSpendingCustomization'    => 'EmailSpendingCustomization',
        'AdvancedCustomization'         => 'AdvancedCustomization',
        'AdvancedEarningCustomization'  => 'AdvancedEarningCustomization',
        'AdvancedSpendingCustomization' => 'AdvancedSpendingCustomization',
        'AdvancedReferralCustomization' => 'AdvancedReferralCustomization',
        'AdvancedTabCustomization'      => 'AdvancedTabCustomization',
        'VIP_Program'                   => 'VIP_Program',
        'RewardsPage'                   => 'RewardsPage',
        'VariableDiscountCoupons'       => 'VariableDiscountCoupons',
        'InsightsReports'               => 'InsightsReports',
        'HTML_Editor'                   => 'HTML_Editor',
        'CustomDomain'                  => 'CustomDomain',
        'PointsExpiration'              => 'PointsExpiration',
    ],

    'features' => [
        'growth'   => [
            [
                'title'   => 'Referral Program',
                'tooltip' => 'Drive new revenue to your store by allowing customers to refer their friends to your business.',
            ],
            [
                'title'   => 'Email Customization',
                'tooltip' => 'Customize all aspects of your emails including the logo, text and colors.',
            ],
            [
                'title'   => 'Earning Limits',
                'tooltip' => 'Set limits on how many points a customer can receive in a set period of time.',
            ],
            [
                'title'   => 'Customer Segmentation',
                'tooltip' => 'Create unique earning & spending rewards for specific customers, products, or categories.',
            ],
            [
                'title'   => 'Import Existing Customers',
                'tooltip' => 'Upload all of your customers into Lootly so they can retroactively get points for their past purchases.',
            ],
            [
                'title'   => 'Integrations',
                'tooltip' => 'Connect other great apps to expand functionality for your reward program, such as giving points for Writing a Review.',
            ],
            [
                'title'   => 'Remove Lootly Branding',
                'tooltip' => 'Remove all mentions of Lootly on your rewards program including the Widget and Emails. ',
            ],
        ],
        'ultimate' => [
            [
                'title'   => 'VIP Program',
                'tooltip' => 'Create special tiers with unique perks to reward your most loyal customers.',
            ],
            [
                'title'   => 'Points Expiration',
                'tooltip' => 'Set an expiration time for all points earned in your store and send automatic reminders to dormant customers to spend their points.',
            ],
            [
                'title'   => 'Advanced Customization',
                'tooltip' => 'Upload background images, icon images and more to fully customize all aspects of your loyalty program.',
            ],
            [
                'title'   => 'Variable Discount Coupons',
                'tooltip' => 'Variable Coupons allow customers to redeem any amount of points they have for a discount at your store. For example: “Get $1 Off per 100 points redeemed”.',
            ],
            [
                'title'   => 'HTML Editor Access',
                'tooltip' => 'Customize every aspect of your emails and reward page with our HTML Editor.',
            ],
            [
                'title'   => 'Insights & Reports',
                'tooltip' => 'Get an in-depth understanding of how well Lootly is working for your business, including an overview of New Revenue, Orders, Referrals, Investments and more.',
            ],
            [
                'title'   => 'Rewards Page',
                'tooltip' => 'Display an overview of your program on a dedicated page on your site including a How it Works and FAQ section.',
            ],
            [
                'title'   => 'Employee Access',
                'tooltip' => 'Invite other users to access your account.',
            ],
            [
                'title'   => 'Priority Support',
                'tooltip' => 'Extended support hours and priority queue support.',
            ],
        ],
        'enterprise'  => [
            [
                'title'   => 'Custom Sender Domain',
                'tooltip' => 'Send emails using your own domain and customize your referral link to match your site.',
            ],
            [
                'title'   => 'SMS Alerts (coming soon)',
                'tooltip' => '',
            ],
            [
                'title'   => 'API Access',
                'tooltip' => 'Build custom rules and integrations with access to our API.',
            ],
            [
                'title'   => 'Dedicated Account Manager',
                'tooltip' => 'Gain access to a dedicated loyalty expert who is responsible for making optimizations, customizing the design and to ensure everything is running smoothly.',
            ],
            [
                'title'   => 'Fully Managed Implementation',
                'tooltip' => 'Our enterprise account managers can help you fully setup your entire program including reward components, design customization, customer imports, and more.',
            ],
        ],
    ],

];
