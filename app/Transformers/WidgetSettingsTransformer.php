<?php

namespace App\Transformers;

use App\Merchant;
use App\Models\WidgetSettings;
use App\User;
use League\Fractal\TransformerAbstract;

class WidgetSettingsTransformer extends TransformerAbstract
{

    protected $availableIncludes = [];

    public function transform(WidgetSettings $r)
    {
        return [
            'id' => $r->id,
            'merchant_id' => $r->merchant_id,
            'signup_link' => $r->widget_welcome_signup_link,
            'login_link' => $r->widget_welcome_login_link,
            'tab' => [
                'rewards_visible' => $r->tab_rewards_visible,
                'enable_reminders' => $r->enable_reminders,
                'position' => $r->tab_position,
                'text' => $r->tab_text,
                'bg_color' => $r->tab_bg_color,
                'font_color' => $r->tab_font_color,
                'side_spacing' => $r->tab_side_spacing,
                'bottom_spacing' => $r->tab_bottom_spacing,
                'display_on' => $r->tab_display_on,
                'desktop_layout' => $r->tab_desktop_layout,
                'custom_icon' => $r->tab_custom_icon,
                'icon' => $r->tab_icon,
                'icon_name' => $r->tab_icon_name,
            ],
            'widget' => [
                'not-logged-in' => [
                    'welcome' => [
                        'header' => [
                            'title' => $r->widget_welcome_header_title,
                            'subtitle' => $r->widget_welcome_header_subtitle,
                        ],
                        'title' => $r->widget_welcome_title,
                        'subtitle' => $r->widget_welcome_subtitle,
                        'button_text' => $r->widget_welcome_button_text,
                        'login' => $r->widget_welcome_login,
                        'loginLinkText' => $r->widget_welcome_login_link_text,
                        'pointsRewardsTitle' => $r->widget_welcome_points_rewards_title,
                        'pointsRewardsSubtitle' => $r->widget_welcome_points_rewards_subtitle,
                        'pointsRewardsEarningTitle' => $r->widget_welcome_points_rewards_earning_title,
                        'pointsRewardsSpendingTitle' => $r->widget_welcome_points_rewards_spending_title,
                        'vipTitle' => $r->widget_welcome_vip_title,
                        'vipSubtitle' => $r->widget_welcome_vip_subtitle,
                        'referralTitle' => $r->widget_welcome_referral_title,
                        'referralSubtitle' => $r->widget_welcome_referral_subtitle,
                        'position' => $r->widget_welcome_position,
                        'background' => $r->widget_welcome_background,
                        'background_name' => $r->widget_welcome_background_name,
                        'background_opacity' => $r->widget_welcome_background_opacity,
                    ],
                    'ways_to_earn' => [
                        'title' => $r->widget_ways_to_earn_title,
                        'text' => $r->widget_ways_to_earn_text,
                        'position' => $r->widget_ways_to_earn_position,
                    ],
                    'ways_to_spend' => [
                        'title' => $r->widget_ways_to_spend_title,
                        'text' => $r->widget_ways_to_spend_text,
                        'position' => $r->widget_ways_to_spend_position,
                    ],
                    'referral_receiver' => [
                        'text' => $r->widget_rr_text,
                        'button_text' => $r->widget_rr_button_text,
                        'background' => $r->widget_rr_background,
                        'background_name' => $r->widget_rr_background_name,
                        'background_opacity' => $r->widget_rr_background_opacity,
                    ]
                ],
                'logged-in' => [
                    'welcome' => [
                        'text' => $r->widget_logged_welcome_text,
                        'position' => $r->widget_logged_welcome_position,
                        'icon' => $r->widget_logged_welcome_icon,
                        'icon_name' => $r->widget_logged_welcome_icon_name,
                        'background' => $r->widget_logged_welcome_background,
                        'background_name' => $r->widget_logged_welcome_background_name,
                        'background_opacity' => $r->widget_logged_welcome_background_opacity,
                    ],
                    'points' => [
                        'balance_text' => $r->widget_logged_points_balance_text,
                        'available_text' => $r->widget_logged_points_available_text,
                        'earn_button_text' => $r->widget_logged_points_earn_button_text,
                        'spend_button_text' => $r->widget_logged_points_spend_button_text,
                        'rewards_button_text' => $r->widget_logged_points_rewards_button_text,

                        'redeem_tab_text' => $r->widget_logged_points_reedem_tab_text,
                        'rewards_tab_button' => $r->widget_logged_points_rewards_tab_button,
                        'earn_tab_text' => $r->widget_logged_points_earn_tab_text,
                        'earn_tab_button' => $r->widget_logged_points_earn_tab_button,
                        'rewards_title' => $r->widget_logged_my_rewards_title,
                        'rewards_text' => $r->widget_logged_my_rewards_text,
                        'no_rewards_text' => $r->widget_logged_no_rewards_text,
                        'reward_view_button' => $r->widget_logged_reward_view_button,
                        'points_needed_text' => $r->widget_logged_points_needed_text,
                        'points_activity_title' => $r->widget_logged_points_activity_title,                        
                    ],
                    'vip' => [
                        'button_text' => $r->widget_logged_vip_button_text,
                        'background' => $r->widget_logged_vip_background,
                        'background_name' => $r->widget_logged_vip_background_name,
                        'background_opacity' => $r->widget_logged_vip_background_opacity,
                    ],
                    'referrals' => [
                        'main_text' => $r->widget_logged_referral_main_text,
                        'receiver_text' => $r->widget_logged_referral_receiver_text,
                        'sender_text' => $r->widget_logged_referral_sender_text,
                        'copy_button' => $r->widget_logged_referral_copy_button,
                        'link_text' => $r->widget_logged_referral_link_text,
                        'background' => $r->widget_logged_referral_background,
                        'background_name' => $r->widget_logged_referral_background_name,
                        'background_opacity' => $r->widget_logged_referral_background_opacity,
                    ],
                    'how_it_works' => [
                        'title' => $r->widget_how_it_works_title,
                        'text' => $r->widget_how_it_works_text,
                        'position' => $r->widget_how_it_works_position
                    ],
                    'coupon' => [
                        'title' => $r->widget_logged_coupon_title,
                        'copy_button' => $r->widget_logged_coupon_copy_button,
                        'body_text' => $r->widget_logged_coupon_body_text,
                        'button_text' => $r->widget_logged_coupon_button_text
                    ]
                ]
            ],
            'branding' => [
                'primary_color' => $r->brand_primary_color,
                'secondary_color' => $r->brand_secondary_color,
                'header_bg' => $r->brand_header_bg,
                'header_bg_font_color ' => $r->brand_header_bg_font_color,
                'button_color' => $r->brand_button_color,
                'button_font_color' => $r->brand_button_font_color,
                'link_color' => $r->brand_link_color,
                'font' => $r->brand_font,
                'custom_css' => $r->custom_css,
                'remove_in_widget' => $r->brand_remove_in_widget,
            ],
            'created_at' => $r->created_at,
            'updated_at' => $r->updated_at,
        ];
    }

}