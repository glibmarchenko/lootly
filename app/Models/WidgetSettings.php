<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Merchant;

class WidgetSettings extends Model
{
    const TAB_POSITIONS = [
        'left',
        'center',
        'right',
    ];

    const WELCOME_POSITIONS = [
        'left',
        'center',
        'right',
    ];

    const OVERVIEW_POSITIONS = [
        'left',
        'center',
        'right',
    ];

    const BRANDING_FONTS = [
        'lato',
        'courier',
        'proxima-nova',
        'Montserrat',
        'arial',
        'JUNGLEFE',
    ];

    const DEFAULT_SETTINGS = [
        // Tab settings
        'tab_rewards_visible'    => 1,
        'tab_position'           => 'right',
        'tab_text'               => 'Rewards',
        'tab_bg_color'           => '#2b69d1',
        'tab_font_color'         => '#FFFFFF',
        'tab_side_spacing'       => '30',
        'tab_bottom_spacing'     => '30',
        'tab_display_on'         => 'desktop-mobile',
        'tab_desktop_layout'     => 'icon-text',
        'tab_custom_icon'        => 0,
        'tab_icon'               => '',
        'tab_icon_name'          => '',

        // Widget settings
        'widget_welcome_header_title'            => 'Welcome to',
        'widget_welcome_header_subtitle'         => '{company}',
        'widget_welcome_title'                   => 'Join our Rewards Program',
        'widget_welcome_subtitle'                => 'Access existing perks, savings and rewards just by shopping with us!',
        'widget_welcome_button_text'             => 'Create an Account',
        'widget_welcome_login'                   => 'Already have an account?',
        'widget_welcome_points_rewards_title'    => '{points-name} & Rewards',
        'widget_welcome_points_rewards_subtitle' => 'Earn {points-name} for completing actions, and turn your {points-name} into rewards.',
        'widget_welcome_vip_title'               => 'VIP Tiers',
        'widget_welcome_vip_subtitle'            => 'Gain access to exclusive rewards. Reach higher tiers for more exlucisve perks.',
        'widget_welcome_referral_title'          => 'Referrals',
        'widget_welcome_referral_Subtitle'       => 'Tell your friends about us and earn rewards',

        'widget_welcome_position'           => 'center',
        'widget_welcome_background'         => '',
        'widget_welcome_background_name'    => '',
        'widget_welcome_background_opacity' => '100%',

        'widget_ways_to_earn_text'      => 'Earn more {points-name} for completing different actions with our rewards program.',
        'widget_ways_to_earn_position'  => 'left',
        'widget_ways_to_spend_text'     => 'Redeem your {points-name} into awesome rewards.',
        'widget_ways_to_spend_position' => 'left',

        'widget_rr_text'               => '{referral-name} has sent you a coupon for {referral-discount}. Get your coupon now.',
        'widget_rr_button_text'        => 'Get My Coupon',
        'widget_rr_background'         => '',
        'widget_rr_background_name'    => '',
        'widget_rr_background_opacity' => '100%',

        'widget_logged_welcome_text'               => 'Welcome back {customer-name}',
        'widget_logged_welcome_position'           => 'center',
        'widget_logged_welcome_icon'               => '',
        'widget_logged_welcome_icon_name'          => '',
        'widget_logged_welcome_background'         => '',
        'widget_logged_welcome_background_name'    => '',
        'widget_logged_welcome_background_opacity' => '100%',

        'widget_logged_points_balance_text'        => 'Your {points-name} balance',
        'widget_logged_points_available_text'      => 'Available at',
        'widget_logged_points_earn_button_text'    => 'Earn more {points-name}',
        'widget_logged_points_spend_button_text'   => 'Spend {points-name}',
        'widget_logged_points_rewards_button_text' => 'My Rewards',

        'widget_logged_vip_button_text'        => 'See Benefits',
        'widget_logged_vip_background'         => '',
        'widget_logged_vip_background_name'    => '',
        'widget_logged_vip_background_opacity' => '100%',

        'widget_logged_referral_main_text'          => 'Tell your friends about us and earn rewards',
        'widget_logged_referral_receiver_text'      => 'They will receive',
        'widget_logged_referral_sender_text'        => 'You will receive',
        'widget_logged_referral_link_text'          => 'How our referral program works',
        'widget_logged_referral_background'         => '',
        'widget_logged_referral_background_name'    => '',
        'widget_logged_referral_background_opacity' => '100%',

        // Branding settings
        'brand_primary_color'    => '#2b69d1',
        'brand_secondary_color'  => '#3d3d3d',
        'brand_header_bg'  => '#2b69d1',
        'brand_header_bg_font_color'  => '#FFFFFF',
        'brand_button_color'  => '#2b69d1',
        'brand_button_font_color'  => '#FFFFFF',
        'brand_link_color'       => '#2b69d1',
        'brand_font'             => 'lato',
        'brand_remove_in_widget' => 0,
    ];

    protected $fillable = [
        'merchant_id',

        'tab_rewards_visible',
        'tab_position',
        'tab_text',
        'tab_bg_color',
        'tab_font_color',
        'tab_side_spacing',
        'tab_bottom_spacing',
        'tab_display_on',
        'tab_desktop_layout',
        'tab_custom_icon',
        'tab_icon',
        'tab_icon_name',

        'widget_welcome_header_title',
        'widget_welcome_header_subtitle',
        'widget_welcome_title',
        'widget_welcome_subtitle',
        'widget_welcome_button_text',
        'widget_welcome_login',
        'widget_welcome_position',
        'widget_welcome_background',
        'widget_welcome_background_name',
        'widget_welcome_background_opacity',

        'widget_overview_text',
        'widget_overview_position',

        'widget_rr_text',
        'widget_rr_button_text',
        'widget_rr_background',
        'widget_rr_background_name',
        'widget_rr_background_opacity',

        'brand_primary_color',
        'brand_secondary_color',
        'brand_header_bg',
        'brand_header_bg_font_color',
        'brand_button_color',
        'brand_button_font_color',
        'brand_link_color',
        'brand_font',
        'brand_remove_in_widget',

        'widget_logged_welcome_text',
        'widget_logged_welcome_position',
        'widget_logged_welcome_icon',
        'widget_logged_welcome_icon_name',
        'widget_logged_welcome_background',
        'widget_logged_welcome_background_name',
        'widget_logged_welcome_background_opacity',
        'widget_logged_points_balance_text',
        'widget_logged_points_available_text',
        'widget_logged_points_earn_button_text',
        'widget_logged_points_spend_button_text',
        'widget_logged_points_rewards_button_text',
        'widget_logged_vip_button_text',
        'widget_logged_vip_background',
        'widget_logged_vip_background_name',
        'widget_logged_vip_background_opacity',
        'widget_logged_referral_main_text',
        'widget_logged_referral_receiver_text',
        'widget_logged_referral_sender_text',
        'widget_logged_referral_link_text',
        'widget_logged_referral_background',
        'widget_logged_referral_background_name',
        'widget_logged_referral_background_opacity',
    ];

    public function merchant()
    {
        return $this->hasOne(Merchant::class, 'id', 'merchant_id')->withDefault();
    }
}
