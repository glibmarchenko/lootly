<?php

namespace App\Repositories;

use App\Models\WidgetSettings;
use App\Contracts\Repositories\WidgetSettingsRepository as WidgetSettingsRepositoryContract;
use App\Services\Amazon\UploadFile;

class WidgetSettingsRepository implements WidgetSettingsRepositoryContract
{
    protected $baseQuery;

    public function __construct()
    {
        $this->baseQuery = WidgetSettings::query();
    }

    /*
    public function getDefaults(){
        $default_settings = new WidgetSettings();
        $default_settings->fill(WidgetSettings::DEFAULT_SETTINGS);
        return $default_settings;
    }
    */

    /**
     * @param string $code
     */
    public function first($merchantObj)
    {
        if (! $merchantObj) {
            return null;
        }
        $widget_settings = $this->baseQuery->where('merchant_id', '=', $merchantObj->id)
            ->orderBy('created_at', 'desc')
            ->first();

        return $widget_settings;
    }

    /*
    public function findByMerchantId($merchantId)
    {
        if (!$merchantId) {
            return null;
        }

        return WidgetSettings::where('merchant_id', $merchantId)->orderBy('created_at', 'desc')->first();
    }
    */

    public function createOrUpdateTabSettings($merchantObj, array $data)
    {
        if (! $merchantObj) {
            return null;
        }

        $widget_settings = $this->baseQuery->where('merchant_id', '=', $merchantObj->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $widget_settings) {
            $widget_settings = new WidgetSettings();
            $widget_settings->merchant_id = $merchantObj->id;
            $widget_settings->save();
        }

        $new_icon_url = null;
        $amazon = new UploadFile();
        $file = isset($data['new_icon']) && $data['new_icon'] ? $data['new_icon'] : null;
        if ($file && isset($data['custom_icon']) && $data['custom_icon']) {
            $new_icon_url = $amazon->upload($merchantObj, $file, $widget_settings->id);
        }

        $widget_settings->tab_rewards_visible = isset($data['status']) && $data['status'] ? true : false;
        $widget_settings->enable_reminders = isset($data['enable_reminders']) && $data['enable_reminders'] ? true : false;
        $widget_settings->tab_position = isset($data['position']) ? trim($data['position']) : null;
        // $widget_settings->tab_hide_on_mobile = isset($data['mobileDisplay']) && $data['mobileDisplay'] ? true : false;
        $widget_settings->tab_text = isset($data['text']) ? trim($data['text']) : null;

        $widget_settings->tab_side_spacing = isset($data['side_spacing']) ? trim($data['side_spacing']) : null;
        $widget_settings->tab_bottom_spacing = isset($data['bottom_spacing']) ? trim($data['bottom_spacing']) : null;
        $widget_settings->tab_display_on = isset($data['display_on']) ? trim($data['display_on']) : null;
        $widget_settings->tab_desktop_layout = isset($data['desktop_layout']) ? trim($data['desktop_layout']) : null;
        $widget_settings->tab_custom_icon = isset($data['custom_icon']) && $data['custom_icon'] ? true : false;

        $widget_settings->tab_bg_color = isset($data['tabColor']) ? trim($data['tabColor']) : null;
        $widget_settings->tab_font_color = isset($data['tabFontColor']) ? trim($data['tabFontColor']) : null;

        if(isset($data['custom_icon']) && $data['custom_icon']) {
            if ($new_icon_url) {
                $this->deleteWidgetSettingsImage($widget_settings->tab_icon);
                $widget_settings->tab_icon = $new_icon_url;
                $widget_settings->tab_icon_name = isset($data['icon_name']) ? $data['icon_name'] : null;
            } else {
                if (! isset($data['icon']) || ! trim($data['icon'])) {
                    $this->deleteWidgetSettingsImage($widget_settings->tab_icon);
                    $widget_settings->tab_icon = null;
                    $widget_settings->tab_icon_name = null;
                }
            }
        } else {
            $this->deleteWidgetSettingsImage($widget_settings->tab_icon);
            $widget_settings->tab_icon = isset($data['icon']) ? trim($data['icon']) : null;
            $widget_settings->tab_icon_name = isset($data['icon_name']) ? $data['icon_name'] : null;
        }

        $widget_settings->save();
        $widget_settings->fresh();

        return $widget_settings;
    }

    public function deleteTabIcon($widget_settings_id)
    {
        $amazon = new UploadFile();
        $path = $this->getTabIconNameById($widget_settings_id);
        $amazon->delete($path);

        return $this->baseQuery->where('id', '=', $widget_settings_id)->update([
            'tab_icon'      => null,
            'tab_icon_name' => null,
        ]);
    }

    public function getTabIconNameById($id)
    {
        $icon = $this->baseQuery->findOrFail($id);
        $split_path = explode('/', $icon->tab_icon);
        $index = count($split_path);
        $icon_name = $split_path[$index - 1];

        return $icon_name;
    }

    public function createOrUpdateWidgetSettings($merchantObj, array $data)
    {
        if (! $merchantObj) {
            return null;
        }

        $widget_settings = $this->baseQuery->where('merchant_id', '=', $merchantObj->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $widget_settings) {
            $widget_settings = new WidgetSettings();
            $widget_settings->merchant_id = $merchantObj->id;
            $widget_settings->save();
        }

        $new_welcome_background_url = null;
        $new_referral_background_url = null;
        $amazon = new UploadFile();

        $welcome_background = isset($data['welcome']['new_background']) && ($data['welcome']['new_background']) ? $data['welcome']['new_background'] : null;
        if ($welcome_background) {
            $new_welcome_background_url = $amazon->upload($merchantObj, $welcome_background, $widget_settings->id);
        }

        $referral_background = isset($data['referral']['new_background']) && ($data['referral']['new_background']) ? $data['referral']['new_background'] : null;
        if ($referral_background) {
            $new_referral_background_url = $amazon->upload($merchantObj, $referral_background, $widget_settings->id);
        }

        $widget_settings->widget_welcome_header_title = isset($data['welcome']['header']['title']) ? trim($data['welcome']['header']['title']) : null;
        $widget_settings->widget_welcome_header_subtitle = isset($data['welcome']['header']['subtitle']) ? trim($data['welcome']['header']['subtitle']) : null;
       
        $widget_settings->widget_welcome_title = isset($data['welcome']['title']) ? trim($data['welcome']['title']) : null;
        $widget_settings->widget_welcome_subtitle = isset($data['welcome']['subtitle']) ? trim($data['welcome']['subtitle']) : null;
        $widget_settings->widget_welcome_button_text = isset($data['welcome']['buttonText']) ? trim($data['welcome']['buttonText']) : null;
        $widget_settings->widget_welcome_login = isset($data['welcome']['login']) ? trim($data['welcome']['login']) : null;
        $widget_settings->widget_welcome_login_link_text = isset($data['welcome']['loginLinkText']) ? trim($data['welcome']['loginLinkText']) : null;
        
        $widget_settings->widget_welcome_login_link = isset($data['welcome']['loginLink']) ? trim($data['welcome']['loginLink']) : null;
        $widget_settings->widget_welcome_signup_link = isset($data['welcome']['signupLink']) ? trim($data['welcome']['signupLink']) : null;

        $widget_settings->widget_welcome_button_text = isset($data['welcome']['buttonText']) ? trim($data['welcome']['buttonText']) : null;

        $widget_settings->widget_welcome_position = isset($data['welcome']['position']) ? trim($data['welcome']['position']) : null;
        $widget_settings->widget_welcome_background_opacity = isset($data['welcome']['background_opacity']) ? trim($data['welcome']['background_opacity']) : null;
        if ($new_welcome_background_url) {
            $this->deleteWidgetSettingsImage($widget_settings->widget_welcome_background);
            $widget_settings->widget_welcome_background = $new_welcome_background_url;
            $widget_settings->widget_welcome_background_name = isset($data['welcome']['background_name']) ? $data['welcome']['background_name'] : null;
        } else {
            if (! isset($data['welcome']['background']) || ! trim($data['welcome']['background'])) {
                $this->deleteWidgetSettingsImage($widget_settings->widget_welcome_background);
                $widget_settings->widget_welcome_background = null;
                $widget_settings->widget_welcome_background_name = null;
            }
        }

        $widget_settings->widget_welcome_points_rewards_title = isset($data['welcome']['pointsRewardsTitle']) ? trim($data['welcome']['pointsRewardsTitle']) : null;
        $widget_settings->widget_welcome_points_rewards_subtitle = isset($data['welcome']['pointsRewardsSubtitle']) ? trim($data['welcome']['pointsRewardsSubtitle']) : null;
        
        $widget_settings->widget_welcome_points_rewards_earning_title = isset($data['welcome']['pointsRewardsEarningTitle']) ? trim($data['welcome']['pointsRewardsEarningTitle']) : null;
        $widget_settings->widget_welcome_points_rewards_spending_title = isset($data['welcome']['pointsRewardsSpendingTitle']) ? trim($data['welcome']['pointsRewardsSpendingTitle']) : null;

        $widget_settings->widget_welcome_vip_title = isset($data['welcome']['vipTitle']) ? trim($data['welcome']['vipTitle']) : null;
        $widget_settings->widget_welcome_vip_subtitle = isset($data['welcome']['vipSubtitle']) ? trim($data['welcome']['vipSubtitle']) : null;
        $widget_settings->widget_welcome_referral_title = isset($data['welcome']['referralTitle']) ? trim($data['welcome']['referralTitle']) : null;
        $widget_settings->widget_welcome_referral_subtitle = isset($data['welcome']['referralSubtitle']) ? trim($data['welcome']['referralSubtitle']) : null;
        
        $widget_settings->widget_ways_to_earn_title = isset($data['waysToEarn']['title']) ? trim($data['waysToEarn']['title']) : null;
        $widget_settings->widget_ways_to_earn_text = isset($data['waysToEarn']['text']) ? trim($data['waysToEarn']['text']) : null;
        $widget_settings->widget_ways_to_earn_position = isset($data['waysToEarn']['position']) ? trim($data['waysToEarn']['position']) : null;

        $widget_settings->widget_ways_to_spend_title = isset($data['waysToSpend']['title']) ? trim($data['waysToSpend']['title']) : null;
        $widget_settings->widget_ways_to_spend_text = isset($data['waysToSpend']['text']) ? trim($data['waysToSpend']['text']) : null;
        $widget_settings->widget_ways_to_spend_position = isset($data['waysToSpend']['position']) ? trim($data['waysToSpend']['position']) : null;

        $widget_settings->widget_rr_text = isset($data['referral']['text']) ? trim($data['referral']['text']) : null;
        $widget_settings->widget_rr_button_text = isset($data['referral']['buttonText']) ? trim($data['referral']['buttonText']) : null;
        $widget_settings->widget_rr_background_opacity = isset($data['referral']['background_opacity']) ? trim($data['referral']['background_opacity']) : null;
        if ($new_referral_background_url) {
            $this->deleteWidgetSettingsImage($widget_settings->widget_rr_background);
            $widget_settings->widget_rr_background = $new_referral_background_url;
            $widget_settings->widget_rr_background_name = isset($data['referral']['background_name']) ? $data['referral']['background_name'] : null;
        } else {
            if (! isset($data['referral']['background']) || ! trim($data['referral']['background'])) {
                $this->deleteWidgetSettingsImage($widget_settings->widget_rr_background);
                $widget_settings->widget_rr_background = null;
                $widget_settings->widget_rr_background_name = null;
            }
        }

        $widget_settings->save();
        $widget_settings->fresh();

        return $widget_settings;
    }

    public function createOrUpdateWidgetLoggedSettings($merchantObj, array $data)
    {
        if (! $merchantObj) {
            return null;
        }

        $widget_settings = $this->baseQuery->where('merchant_id', '=', $merchantObj->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $widget_settings) {
            $widget_settings = new WidgetSettings();
            $widget_settings->merchant_id = $merchantObj->id;
            $widget_settings->save();
        }

        $new_welcome_background_url = null;
        $new_welcome_icon_url = null;
        $new_vip_background_url = null;
        $new_referral_background_url = null;
        $amazon = new UploadFile();

        $welcome_background = isset($data['welcome']['new_background']) && ($data['welcome']['new_background']) ? $data['welcome']['new_background'] : null;
        if ($welcome_background) {
            $new_welcome_background_url = $amazon->upload($merchantObj, $welcome_background, $widget_settings->id);
        }

        $welcome_icon = isset($data['welcome']['new_icon']) && ($data['welcome']['new_icon']) ? $data['welcome']['new_icon'] : null;
        if ($welcome_icon) {
            $new_welcome_icon_url = $amazon->upload($merchantObj, $welcome_icon, $widget_settings->id);
        }

        $vip_background = isset($data['vip']['new_background']) ? $data['vip']['new_background'] : null;
        if ($vip_background) {
            $new_vip_background_url = $amazon->upload($merchantObj, $vip_background, $widget_settings->id);
        }

        $referral_background = isset($data['referral']['new_background']) ? $data['referral']['new_background'] : null;
        if ($referral_background) {
            $new_referral_background_url = $amazon->upload($merchantObj, $referral_background, $widget_settings->id);
        }

        $arr = [
            $new_welcome_background_url,
            $new_welcome_icon_url,
            $new_vip_background_url,
            $new_referral_background_url,
        ];

        $widget_settings->widget_logged_welcome_text = isset($data['welcome']['text']) ? trim($data['welcome']['text']) : null;
        $widget_settings->widget_logged_welcome_position = isset($data['welcome']['position']) ? trim($data['welcome']['position']) : null;
        $widget_settings->widget_logged_welcome_background_opacity = isset($data['welcome']['background_opacity']) ? trim($data['welcome']['background_opacity']) : null;
        if ($new_welcome_background_url) {
            $this->deleteWidgetSettingsImage($widget_settings->widget_logged_welcome_background);
            $widget_settings->widget_logged_welcome_background = $new_welcome_background_url;
            $widget_settings->widget_logged_welcome_background_name = isset($data['welcome']['background_name']) ? $data['welcome']['background_name'] : null;
        } else {
            if (! isset($data['welcome']['background']) || ! trim($data['welcome']['background'])) {
                $this->deleteWidgetSettingsImage($widget_settings->widget_logged_welcome_background);
                $widget_settings->widget_logged_welcome_background = null;
                $widget_settings->widget_logged_welcome_background_name = null;
            }
        }

        if ($new_welcome_icon_url) {
            $this->deleteWidgetSettingsImage($widget_settings->widget_logged_welcome_icon);
            $widget_settings->widget_logged_welcome_icon = $new_welcome_icon_url;
            $widget_settings->widget_logged_welcome_icon_name = isset($data['welcome']['icon_name']) ? $data['welcome']['icon_name'] : null;
        } else {
            if (! isset($data['welcome']['icon']) || ! trim($data['welcome']['icon'])) {
                $this->deleteWidgetSettingsImage($widget_settings->widget_logged_welcome_icon);
                $widget_settings->widget_logged_welcome_icon = null;
                $widget_settings->widget_logged_welcome_icon_name = null;
            }
        }

        $widget_settings->widget_logged_points_balance_text = isset($data['points']['balanceText']) ? trim($data['points']['balanceText']) : null;
        $widget_settings->widget_logged_points_available_text = isset($data['points']['availableText']) ? trim($data['points']['availableText']) : null;
        $widget_settings->widget_logged_points_earn_button_text = isset($data['points']['earnButtonText']) ? trim($data['points']['earnButtonText']) : null;
        $widget_settings->widget_logged_points_spend_button_text = isset($data['points']['spendButtonText']) ? trim($data['points']['spendButtonText']) : null;
        $widget_settings->widget_logged_points_rewards_button_text = isset($data['points']['rewardsButtonText']) ? trim($data['points']['rewardsButtonText']) : null;

        $widget_settings->widget_logged_points_reedem_tab_text = isset($data['points']['redeemTabText']) ? trim($data['points']['redeemTabText']) : null;
        $widget_settings->widget_logged_points_rewards_tab_button = isset($data['points']['rewardsTabButton']) ? trim($data['points']['rewardsTabButton']) : null;
        $widget_settings->widget_logged_points_earn_tab_text = isset($data['points']['earnTabText']) ? trim($data['points']['earnTabText']) : null;
        $widget_settings->widget_logged_points_earn_tab_button = isset($data['points']['earnTabButton']) ? trim($data['points']['earnTabButton']) : null;
        $widget_settings->widget_logged_my_rewards_title = isset($data['points']['rewardsTitle']) ? trim($data['points']['rewardsTitle']) : null;
        $widget_settings->widget_logged_my_rewards_text = isset($data['points']['rewardsText']) ? trim($data['points']['rewardsText']) : null;
        $widget_settings->widget_logged_no_rewards_text = isset($data['points']['noRewardsText']) ? trim($data['points']['noRewardsText']) : null;
        $widget_settings->widget_logged_reward_view_button = isset($data['points']['rewardViewButton']) ? trim($data['points']['rewardViewButton']) : 'View';

        $widget_settings->widget_logged_points_needed_text = isset($data['points']['pointsNeededText']) ? trim($data['points']['pointsNeededText']) : null;
        $widget_settings->widget_logged_points_activity_title = isset($data['points']['pointsActivityTitle']) ? trim($data['points']['pointsActivityTitle']) : null;


        $widget_settings->widget_logged_vip_button_text = isset($data['vip']['buttonText']) ? trim($data['vip']['buttonText']) : null;
        $widget_settings->widget_logged_vip_background_opacity = isset($data['vip']['background_opacity']) ? trim($data['vip']['background_opacity']) : null;

        if ($new_vip_background_url) {
            $this->deleteWidgetSettingsImage($widget_settings->widget_logged_vip_background);
            $widget_settings->widget_logged_vip_background = $new_vip_background_url;
            $widget_settings->widget_logged_vip_background_name = isset($data['vip']['background_name']) ? trim($data['vip']['background_name']) : null;
        } else {
            if (! isset($data['vip']['background']) || ! trim($data['vip']['background'])) {
                $this->deleteWidgetSettingsImage($widget_settings->widget_logged_vip_background);
                $widget_settings->widget_logged_vip_background = null;
                $widget_settings->widget_logged_vip_background_name = null;
            }
        }

        $widget_settings->widget_logged_referral_main_text = isset($data['referral']['mainText']) ? trim($data['referral']['mainText']) : null;
        $widget_settings->widget_logged_referral_receiver_text = isset($data['referral']['receiverText']) ? trim($data['referral']['receiverText']) : null;
        $widget_settings->widget_logged_referral_sender_text = isset($data['referral']['senderText']) ? trim($data['referral']['senderText']) : null;
        $widget_settings->widget_logged_referral_copy_button = isset($data['referral']['copyButton']) ? trim($data['referral']['copyButton']) : null;
        $widget_settings->widget_logged_referral_link_text = isset($data['referral']['LinkText']) ? trim($data['referral']['LinkText']) : null;
        $widget_settings->widget_logged_referral_background_opacity = isset($data['referral']['background_opacity']) ? trim($data['referral']['background_opacity']) : null;

        if ($new_referral_background_url) {
            $this->deleteWidgetSettingsImage($widget_settings->widget_logged_referral_background);
            $widget_settings->widget_logged_referral_background = $new_referral_background_url;
            $widget_settings->widget_logged_referral_background_name = isset($data['referral']['background_name']) ? $data['referral']['background_name'] : null;
        } else {
            if (! isset($data['referral']['background']) || ! trim($data['referral']['background'])) {
                $this->deleteWidgetSettingsImage($widget_settings->widget_logged_referral_background);
                $widget_settings->widget_logged_referral_background = null;
                $widget_settings->widget_logged_referral_background_name = null;
            }
        }

        $widget_settings->widget_how_it_works_title = isset($data['howItWorks']['title']) ? trim($data['howItWorks']['title']) : null;
        $widget_settings->widget_how_it_works_text = isset($data['howItWorks']['text']) ? trim($data['howItWorks']['text']) : null;
        $widget_settings->widget_how_it_works_position = isset($data['howItWorks']['position']) ? trim($data['howItWorks']['position']) : null;

        $widget_settings->widget_logged_coupon_title = isset($data['coupon']['title']) ? trim($data['coupon']['title']) : null;
        $widget_settings->widget_logged_coupon_copy_button = isset($data['coupon']['copy_button']) ? trim($data['coupon']['copy_button']) : null;
        $widget_settings->widget_logged_coupon_body_text = isset($data['coupon']['body_text']) ? trim($data['coupon']['body_text']) : null;
        $widget_settings->widget_logged_coupon_button_text = isset($data['coupon']['button_text']) ? trim($data['coupon']['button_text']) : null;

        $widget_settings->save();
        $widget_settings->fresh();

        return $widget_settings;
    }

    public function createOrUpdateBrandingSettings($merchantObj, array $data)
    {
        if (! $merchantObj) {
            return null;
        }

        $widget_settings = $this->baseQuery->where('merchant_id', '=', $merchantObj->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $widget_settings) {
            $widget_settings = new WidgetSettings();
            $widget_settings->merchant_id = $merchantObj->id;
            $widget_settings->save();
        }

        $widget_settings->brand_primary_color = isset($data['primaryColor']) ? trim($data['primaryColor']) : null;
        $widget_settings->brand_secondary_color = isset($data['secondaryColor']) ? trim($data['secondaryColor']) : null;
        $widget_settings->brand_header_bg = isset($data['headerBackground']) ? trim($data['headerBackground']) : null;
        $widget_settings->brand_header_bg_font_color = isset($data['headerBackgroundFontColor']) ? trim($data['headerBackgroundFontColor']) : null;
        $widget_settings->brand_button_color = isset($data['buttonColor']) ? trim($data['buttonColor']) : null;
        $widget_settings->brand_button_font_color = isset($data['buttonFontColor']) ? trim($data['buttonFontColor']) : null;

        $widget_settings->tab_bg_color = isset($data['tabColor']) ? trim($data['tabColor']) : null;
        $widget_settings->tab_font_color = isset($data['tabFontColor']) ? trim($data['tabFontColor']) : null;

        $widget_settings->brand_link_color = isset($data['linkColor']) ? trim($data['linkColor']) : null;
        $widget_settings->brand_font = isset($data['font']) ? trim($data['font']) : null;
        $widget_settings->brand_remove_in_widget = isset($data['hideLootlyLogo']) && $data['hideLootlyLogo'] ? true : false;

        $widget_settings->custom_css = isset($data['customCSS']) ? $data['customCSS'] : null;

        $widget_settings->save();
        $widget_settings->fresh();

        return $widget_settings;
    }

    private function deleteWidgetSettingsImage($image = null)
    {
        if ($image) {
            $amazon = new UploadFile();

            $split_path = explode('/', $image);
            $index = count($split_path);
            $path = $split_path[$index - 1];

            $amazon->delete($path);
        }
    }
    public function brandingStatus($merchantObj){
        if (!$merchantObj) {
            return null;
        }

        $widget_settings = $this->baseQuery->where('merchant_id', '=', $merchantObj->id)
            ->orderBy('created_at', 'desc')
            ->first();
        if(isset($widget_settings)) {
            return $widget_settings->brand_remove_in_widget;
        }
        return null;
    }
}
