<?php

namespace App\Helpers;

use App\Exceptions\EmailNotificationException;
use App\Merchant;
use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\EmailBlacklistRepository;
use Html2Text\Html2Text;
use Illuminate\Support\Facades\File;
use App\Repositories\EmailNotificationRepository;
use App\Repositories\MerchantEmailNotificationSettingsRepository;
use App\Repositories\NotificationSettingsRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Postmark\Models\PostmarkException;
use Postmark\PostmarkClient;

class EmailNotification
{
    protected $merchants;

    public function __construct(MerchantRepository $merchants)
    {
        $this->merchants = $merchants;
    }

    public function send($type, $merchant, $to_name, $to_email, $tags = [], $customSubject = null)
    {
        $emailNotificationRepo = new EmailNotificationRepository();
        $emailBlacklistRepo = new EmailBlacklistRepository();
        $notificationSettingsRepo = new NotificationSettingsRepository();
        $merchantEmailNotificationSettingsRepo = new MerchantEmailNotificationSettingsRepository();

        if (! ($merchant instanceof Merchant)) {
            if (! is_integer($merchant)) {
                throw new EmailNotificationException('Access denied for this email notification. Can\'t get information about merchant.', 403);
            }
            try {
                $merchant = $this->merchants->find($merchant);
            } catch (\Exception $e) {
                throw new EmailNotificationException('Access denied for this email notification. '.$e->getMessage(), 403);
            }
        }

        if ($emailBlacklistRepo->check($merchant, $to_email)) {
            Log::info('Email is in blacklist', ['email' => $to_email]);
            return;
        }

        $emailNotification = $emailNotificationRepo->findByType($type);
        if (! $emailNotification) {
            throw new EmailNotificationException('Email notification not found.', 403);
        }

        $emailSubject = $emailNotification->default_subject;
        $emailContentBody = file_exists(storage_path()."/email-notification/".$type."-editable.html") ? File::get(storage_path()."/email-notification/".$type."-editable.html") : '';
        $buttonColor = $emailNotification->default_button_color;
        $buttonText = $emailNotification->default_button_text;

        $notificationSettings = $notificationSettingsRepo->findByType($merchant, $type);

        if ($notificationSettings) {
            $emailSubject = $notificationSettings->subject;
            $emailContentBody = $notificationSettings->body;
            $buttonColor = $notificationSettings->button_color;
            $buttonText = $notificationSettings->button_text;
            $icons = $notificationSettings->icons;
        }

        $htmlEmailTemplate = null;
        if (file_exists(storage_path()."/email-notification/".$type.".html")) {
            $htmlEmailTemplate = File::get(storage_path()."/email-notification/".$type.".html");
        } else {
            throw new EmailNotificationException('No email template found.', 500);
        }

        $merchantSettings = $merchantEmailNotificationSettingsRepo->find($merchant);

        $company_url = env('APP_URL');
        $company_name = $merchant->name;
        $company_logo = url('images/logos/logo-email.png');
        $company_unsubscribe_url = route('unsubscribe.store') . '?email='.$to_email.'&token='.encrypt($to_email.$merchant->id);
        $reply_to = null;
        $from_name = null;
        $branding = '<a href="'.env('APP_URL').'" target="_blank" class="link-black" style="color:#000000; text-decoration:none;"><span class="link-black" style="color:#000000; text-decoration:none;">Powered by Lootly</span></a>';

        if ($merchantSettings) {
            if (trim($merchantSettings->from_name)) {
                $from_name = trim($merchantSettings->from_name);
            }
            if (trim($merchantSettings->company_logo)) {
                $company_logo = trim($merchantSettings->company_logo);
            }
            if (trim($merchantSettings->reply_to_email)) {
                $reply_to = trim($merchantSettings->reply_to_email);
                if (trim($from_name)) {
                    $reply_to = trim($from_name)." <".$reply_to.">";
                }
            }
            if (trim($merchantSettings->remove_branding) && $merchantSettings->remove_branding) {
                $branding = '';
            }
        }

        $button_html = '<table border="0" cellspacing="0" cellpadding="0"><tr><td class="text-button" style="padding: 14px 37px; border-radius: 5px; color:#ffffff; font-family:Arial,sans-serif; font-size:17px; line-height:21px; text-align:center; font-weight:bold;" bgcolor="'.$buttonColor.'"><a href="{button-link}" target="_blank" class="link-white" style="color:#ffffff; text-decoration:none;"><span class="link-white" style="color:#ffffff; text-decoration:none;">'.$buttonText.'</span></a></td></tr></table>';
        $tags_array = [
            '{company}'      => $company_name,
            '{company-name}' => $company_name,
            '{button}'       => $button_html,
        ];

        $systemShortTags = [
            'points_earned'            => $tags_array,
            'points_spent'             => $tags_array,
            'points_reward_available'  => $tags_array,
            'points_point_expiration'  => $tags_array,
            'points_vip_tier_earned'   => $tags_array,
            'referral_share_email'     => $tags_array,
            'referral_receiver_reward' => $tags_array,
            'referral_sender_reward'   => $tags_array,
        ];

        $shortTags = array_merge($tags, $systemShortTags[$type]);
        if (isset($shortTags['{button-link}'])) {
            $tmpTag = $shortTags['{button-link}'];
            unset($shortTags['{button-link}']);
            $shortTags['{button-link}'] = $tmpTag;
            if( $type == 'referral_share_email' ) {
                $shortTags['{button-link}'] .= '?fpl=em';
            }
        }

        if ($type === 'points_earned') {
            if (! isset($shortTags['{next-reward}']) || ! trim($shortTags['{next-reward}'])) {
                // Remove next reward block from email body
                $emailContentBody = preg_replace('/<tr.* ifnextrewardexists[\s\S]*>[\s\S]*<\/tr>/imU', '', $emailContentBody);
            }
        } else {
            if ($type === 'points_vip_tier_earned') {
                if (! isset($shortTags['{available-rewards-list}']) || ! trim($shortTags['{available-rewards-list}'])) {
                    // Remove available rewards
                    $emailContentBody = preg_replace('/<available\-rewards[\s\S]*>[\s\S]*<\/available\-rewards>/imU', '', $emailContentBody);
                }
                if (! isset($shortTags['{benefit-list}']) || ! trim($shortTags['{benefit-list}'])) {
                    // Remove benefits list
                    $emailContentBody = preg_replace('/<tier\-benefits[\s\S]*>[\s\S]*<\/tier\-benefits>/imU', '', $emailContentBody);
                }
            }
        }
        if (isset($customSubject) && trim($customSubject)) {
            $emailSubject = str_replace(array_keys($shortTags), array_values($shortTags), $customSubject);
        } else {
            $emailSubject = str_replace(array_keys($shortTags), array_values($shortTags), $emailSubject);
        }

        $emailContentBodyWithData = str_replace(array_keys($shortTags), array_values($shortTags), $emailContentBody);
        $emailContentBodyWithData = preg_replace('/\$/m', '\\\$', $emailContentBodyWithData);

        $replacementPatterns = [
            '/\[email_title\]/'     => $emailSubject,
            '/\[logo_url\]/'        => $company_url,
            '/\[logo_image\]/'      => $company_logo,
            '/\[body_content\]/'    => $emailContentBodyWithData,
            '/\[unsubscribe_url\]/' => $company_unsubscribe_url,
            '/\[branding\]/'        => $branding,
        ];

        $htmlEmailTemplateWithData = $htmlEmailTemplate;
        foreach ($replacementPatterns as $pattern => $replacement) {
            $htmlEmailTemplateWithData = preg_replace($pattern, $replacement, $htmlEmailTemplateWithData);
        }

        try {
            $plainEmailTemplateWithData = Html2Text::convert($htmlEmailTemplateWithData);
        } catch (\Exception $e) {
            $plainEmailTemplateWithData = strip_tags($htmlEmailTemplateWithData);
        }

        $subject = $emailSubject;
        $client = app('postmark_api')->setup();
        $body = $htmlEmailTemplateWithData;
        $body_txt = $plainEmailTemplateWithData;

        $signature = env('POSTMARK_SIGNATURE');
        if ($from_name) {
            $signature = $from_name.' <'.$signature.'>';
        }

        try {
            $client->sendEmail($signature, "$to_name <$to_email>", $subject, $body, $body_txt, null, true, $reply_to);
        } catch (PostmarkException $ex) {
            throw new EmailNotificationException($ex->postmarkApiErrorCode, $ex->httpStatusCode);
        } catch (\Exception $e) {
            throw new EmailNotificationException($e->getMessage(), 500);
        }

        return;
    }

    public function getImageForIcon($type, $icon)
    {
        $icons = [
            'points_earned'            => [
                'reward-icon' => public_path('images/email-notification/coin.png'),
            ],
            'points_spent'             => [
                'reward-icon' => public_path('images/email-notification/coin.png'),
            ],
            'points_reward_available'  => [
                'reward-icon' => public_path('images/email-notification/coin.png'),
            ],
            'points_point_expiration'  => [],
            'points_vip_tier_earned'   => [
                'vip-tier-icon' => public_path('images/email-notification/vip.png'),
            ],
            'referral_share_email'     => [
                'reward-icon' => public_path('images/email-notification/coin.png'),
            ],
            'referral_receiver_reward' => [
                'reward-icon' => public_path('images/email-notification/coin.png'),
            ],
            'referral_sender_reward'   => [
                'reward-icon' => public_path('images/email-notification/coin.png'),
            ],
        ];

        if (isset($icons[$type]) && isset($icons[$type][$icon])) {
            return $icons[$type][$icon];
        }
    }
}
