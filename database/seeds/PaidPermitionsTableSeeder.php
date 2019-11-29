<?php

use Illuminate\Database\Seeder;

class PaidPermitionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $features = [
            [
                'id' => 1,
                'name' => 'Read Content',
                'type_code' => 'ReadContent',
                'upsell_title' => 'Read Content - Earning Rule',
                'upsell_image' => 'read-content.png',
                'upsell_text' => 'Encourage visitors to read your articles, blog or other important links and reward them with points.',
            ],
            [
                'id' => 2,
                'name' => 'TrustSpot Review',
                'type_code' => 'TrustSpotReview',
                'upsell_title' => 'TrustSpot Review - Earning Rule',
                'upsell_image' => 'trustspot.png',
                'upsell_text' => 'Reward your customers with points for writing a review for your company or product. Connect TrustSpot to your store in 1 click, and begin collecting reviews for your business.',
            ],
            [
                'id' => 3,
                'name' => 'Email Customization',
                'type_code' => 'EmailCustomization',
                'upsell_title' => 'Email Design Customization',
                'upsell_image' => 'email-customization.png',
                'upsell_text' => 'Fully customize all text and colors associated with emails that go out to your customers.',
            ],
            [
                'id' => 4,
                'name' => 'Referral Program',
                'type_code' => 'ReferralProgram',
                'upsell_title' => null,
                'upsell_image' => null,
                'upsell_text' => null,
            ],
            [
                'id' => 5,
                'name' => 'Import Existing Customers',
                'type_code' => 'ImportExistingCustomers',
                'upsell_title' => null,
                'upsell_image' => null,
                'upsell_text' => null,
            ],
            [
                'id' => 6,
                'name' => 'Integrations',
                'type_code' => 'Integrations',
                'upsell_title' => null,
                'upsell_image' => null,
                'upsell_text' => null,
            ],
            [
                'id' => 7,
                'name' => 'Rewards Link',
                'type_code' => 'RewardsLink',
                'upsell_title' => null,
                'upsell_image' => null,
                'upsell_text' => null,
            ],
            [
                'id' => 8,
                'name' => 'Customer Segmentation',
                'type_code' => 'CustomerSegmentation',
                'upsell_title' => 'Customer Segmentation',
                'upsell_image' => 'customer-segmentation.png',
                'upsell_text' => 'Create unique earning actions & spending rewards for customer groups, individual products, or categories of products.',
            ],
            [
                'id' => 9,
                'name' => 'Earning Limits',
                'type_code' => 'EarningLimits',
                'upsell_title' => 'Earning Limits',
                'upsell_image' => 'earning-limits.png',
                'upsell_text' => 'Set limits on how many points a customer can receive in a set period of time.',
            ],
            [
                'id' => 10,
                'name' => 'Remove Lootly Branding',
                'type_code' => 'RemoveLootlyBranding',
                'upsell_title' => 'Remove Lootly Branding',
                'upsell_image' => 'remove-branding.png',
                'upsell_text' => 'Remove all mentions of Lootly on your rewards program including the Widget and Emails.',
            ],
            [
                'id' => 11,
                'name' => 'Remove Lootly Branding for Email',
                'type_code' => 'RemoveLootlyBrandingEmail',
                'upsell_title' => 'Remove Lootly Branding',
                'upsell_image' => 'remove-branding.png',
                'upsell_text' => 'Remove the Lootly logo from the footer of all emails sent to customers.',
            ],
            [
                'id' => 12,
                'name' => 'Email Earning Customization',
                'type_code' => 'EmailEarningCustomization',
                'upsell_title' => 'Email Customization',
                'upsell_image' => 'email-customization.png',
                'upsell_text' => 'Customize the earning action name, reward text and email template.',
            ],
            [
                'id' => 13,
                'name' => 'Email Spending Customization',
                'type_code' => 'EmailSpendingCustomization',
                'upsell_title' => 'Email Customization',
                'upsell_image' => 'email-customization.png',
                'upsell_text' => 'Customize the spending reward name, point requirement text and email template.',
            ],
            [
                'id' => 14,
                'name' => 'Employee Access',
                'type_code' => 'EmployeeAccess',
                'upsell_title' => 'Employee Access',
                'upsell_image' => 'employee-access.png',
                'upsell_text' => ' Invite other users to access your account.',
            ],
            [
                'id' => 15,
                'name' => 'Advanced Customization',
                'type_code' => 'AdvancedCustomization',
                'upsell_title' => 'Advanced Customization',
                'upsell_image' => 'design.png',
                'upsell_text' => 'Customize the main welcome area for all customers by adding your own custom background image.',
            ],
            [
                'id' => 16,
                'name' => 'Advanced Earning Customization',
                'type_code' => 'AdvancedEarningCustomization',
                'upsell_title' => 'Advanced Customization',
                'upsell_image' => 'design.png',
                'upsell_text' => 'Add a custom icon to show for this earning action instead of the default Lootly icon.',
            ],
            [
                'id' => 17,
                'name' => 'Advanced Spending Customization',
                'type_code' => 'AdvancedSpendingCustomization',
                'upsell_title' => 'Advanced Customization',
                'upsell_image' => 'design.png',
                'upsell_text' => 'Add a custom icon to show for this spending reward instead of the default Lootly icon.',
            ],
            [
                'id' => 18,
                'name' => 'Advanced Referral Customization',
                'type_code' => 'AdvancedReferralCustomization',
                'upsell_title' => 'Advanced Customization',
                'upsell_image' => 'design.png',
                'upsell_text' => 'Customize how the referral area will look to your customers, such as adding a custom background image.',
            ],
            [
                'id' => 19,
                'name' => 'Advanced Tab Customization',
                'type_code' => 'AdvancedTabCustomization',
                'upsell_title' => 'Advanced Customization',
                'upsell_image' => 'design.png',
                'upsell_text' => 'Add your custom icon, such as your logo to the tab.',
            ],
            [
                'id' => 20,
                'name' => 'VIP Program',
                'type_code' => 'VIP_Program',
                'upsell_title' => null,
                'upsell_image' => null,
                'upsell_text' => null,
            ],
            [
                'id' => 21,
                'name' => 'Rewards Page',
                'type_code' => 'RewardsPage',
                'upsell_title' => null,
                'upsell_image' => null,
                'upsell_text' => null,
            ],
            [
                'id' => 22,
                'name' => 'Variable Discount Coupons',
                'type_code' => 'VariableDiscountCoupons',
                'upsell_title' => 'Variable Discount Coupon',
                'upsell_image' => 'variable-discount-coupon.png',
                'upsell_text' => 'Variable Coupons allow customers to redeem any amount of points they have for a discount at your store versus a fixed amount.',
            ],
            [
                'id' => 23,
                'name' => 'Insights & Reports',
                'type_code' => 'InsightsReports',
                'upsell_title' => null,
                'upsell_image' => null,
                'upsell_text' => null,
            ],
            [
                'id' => 24,
                'name' => 'HTML Editor',
                'type_code' => 'HTML_Editor',
                'upsell_title' => 'HTML Editor',
                'upsell_image' => 'integrations.png',
                'upsell_text' => 'Fully customize all aspects of your email template with our HTML Editor.',
            ],
            [
                'id' => 25,
                'name' => 'Points Expiration',
                'type_code' => 'PointsExpiration',
                'upsell_title' => 'Points Expiration',
                'upsell_image' => 'points-expiration.png',
                'upsell_text' => 'Set expiration times for points that customers earn on your store. Encourage customers to come back and spend their points with our automated reminder emails.',
            ],
            [
                'id' => 26,
                'name' => 'Custom Domain',
                'type_code' => 'CustomDomain',
                'upsell_title' => 'Custom Sender Domain',
                'upsell_image' => 'advanced-branding.png',
                'upsell_text' => 'Send all emails from Lootly using your own custom domain to ensure all aspects of your Loyalty program are under your branding.',
            ],
            [
                'id' => 27,
                'name' => 'Spending Limits',
                'type_code' => 'SpendingLimits',
                'upsell_title' => 'Spending Limits',
                'upsell_image' => 'earning-limits.png',
                'upsell_text' => 'Define how many times a customer can spend points to obtain this reward, over a period of time.',
            ],
        ];

        \DB::table('paid_permissions')->delete();
        \DB::query("DBCC CHECKIDENT('paid_permissions', RESEED, 0)");
        \DB::table('paid_permissions')->insert($features);
    }
}
