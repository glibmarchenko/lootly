<?php

namespace App\Http\Controllers\Api\Widget;

use App\Http\Controllers\Controller;
use App\Models\TierSettings;
use App\Repositories\Contracts\PointSettingsRepository;
use App\Repositories\Contracts\ReferralSharingRepository;
use App\Repositories\Contracts\WidgetSettingsRepository;
use App\Repositories\Contracts\TierSettingsRepository;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Transformers\PointSettingsTransformer;
use App\Transformers\ReferralSharingTransformer;
use App\Transformers\WidgetSettingsTransformer;
use Illuminate\Http\Request;

class WidgetController extends Controller
{
    private $widgetSettings;

    private $referralSharing;

    private $pointSettings;

    public function __construct(
        WidgetSettingsRepository $widgetSettings,
        ReferralSharingRepository $referralSharing,
        PointSettingsRepository $pointSettings,
        TierSettingsRepository $tierSettings
    ) {
        $this->widgetSettings = $widgetSettings;
        $this->referralSharing = $referralSharing;
        $this->pointSettings = $pointSettings;
        $this->tierSettings = $tierSettings;

        $this->middleware('widget.valid-connection');
    }

    public function widgetSettings(Request $request)
    {
        $merchant_id = $request->get('merchant_id') ?? null;

        $default_settings = $this->widgetSettings->getDefaults();

        try {
            $widgetSettings = $this->widgetSettings->withCriteria([
                new LatestFirst(),
            ])->findWhereFirst([
                'merchant_id' => $merchant_id,
            ]);

            if ($widgetSettings) {
                $widgetSettings = $widgetSettings->toArray();
                foreach ($widgetSettings as $key => $value) {
                    if (isset($widgetSettings[$key])) {
                        $default_settings->{$key} = $value;
                    }
                }
            }
        } catch (\Exception $exception) {
            //
        }

        return fractal($default_settings)->transformWith(new WidgetSettingsTransformer)->toArray();
    }

    public function merchantSettings(Request $request)
    {
        $currency_value = "$"; // Default
        $merchant_id = $request->get('merchant_id');

        $merchant = \App\Merchant::select('currency_id','currency_display_sign')->where('id', $merchant_id)->first();

        if ($merchant->currency_id) {
            $currency = \App\Models\Currency::find($merchant->currency_id);

            if($merchant->currency_display_sign) {
                $currency_value = $currency->currency_sign;
            } else {
                $currency_value = $currency->name;
            }
        }

        return $currency_value;
    }

    public function vipSettings(Request $request) {

        $merchant_id = $request->get('merchant_id') ?? null;

        try {
            $vipSettings = TierSettings::where([
                'merchant_id' => $request->get('merchant_id'),
            ])->first();

        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Cannot get Vip settings',
                'error'   => $exception->getMessage(),
            ], 405);
        }

        return response()->json($vipSettings, 200);

    }

    public function pointSettings(Request $request)
    {
        $merchant_id = $request->get('merchant_id') ?? null;

        $default_settings = $this->pointSettings->getDefaults();

        try {
            $pointSettings = $this->pointSettings->withCriteria([
                new LatestFirst(),
            ])->findWhereFirst([
                'merchant_id' => $merchant_id,
            ]);

            if ($pointSettings) {
                $pointSettings = $pointSettings->toArray();
                foreach ($pointSettings as $key => $value) {
                    if (isset($pointSettings[$key])) {
                        $default_settings->{$key} = $value;
                    }
                }
            }
        } catch (\Exception $exception) {
            //
        }

        return fractal($default_settings)->transformWith(new PointSettingsTransformer)->toArray();
    }

    public function sharingSettings(Request $request)
    {
        try {
            $sharingSettings = $this->referralSharing->findWhereFirst([
                'merchant_id' => $request->get('merchant_id'),
            ]);

            return fractal($sharingSettings)->transformWith(new ReferralSharingTransformer)->toArray();
        } catch (\Exception $exception) {
            return response()->json([
                'data' => [

                    'share_title'       => '',
                    'share_description' => '',

                    'facebook_status'    => false,
                    'facebook_message'   => 'Visit {company} to receive your {reward-name} on your next order. {referral-link}',
                    'facebook_icon'      => '',
                    'facebook_icon_name' => '',

                    'twitter_status'    => false,
                    'twitter_message'   => 'Visit {company} to receive your {reward-name} on your next order. {referral-link}',
                    'twitter_icon'      => '',
                    'twitter_icon_name' => '',

                    'google_status'    => false,
                    'google_message'   => 'Visit {company} to receive your {reward-name} for your next order.',
                    'google_icon'      => '',
                    'google_icon_name' => '',

                    'email_status'  => false,
                    'email_subject' => '{sender-name} just sent you a {reward-name} at {company}',
                    'email_body'    => '{receiver-name}, '."\n".'{sender-name} just sent you a coupon for {reward-name} off your next order at {company}.',

                ],
            ]);
        }
    }
}