<?php

namespace App\Http\Controllers\Settings\Display\EmailNotification;

use App\Exceptions\EmailNotificationException;
use App\Models\NotificationSettings;
use App\Transformers\MerchantEmailNotificationSettingsTransformer;
use App\Transformers\NotificationSettingsTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\Display\EmailNotification\NotificationSettingsCreateRequest;
use App\Http\Requests\Settings\Display\EmailNotification\SaveSettingsRequest;
use App\Repositories\MerchantEmailNotificationSettingsRepository;
use App\Repositories\MerchantRepository;
use App\Repositories\NotificationSettingsRepository;
use App\Models\PaidPermission;
use Illuminate\Support\Facades\Storage;

class EmailNotificationSettingsController extends Controller
{
    protected $merchantRepo;

    protected $notificationSettingsRepo;

    protected $merchantEmailNotificationSettingsRepo;

    protected $currentMerchant;

    public function __construct(
        MerchantRepository $merchantRepository,
        NotificationSettingsRepository $notificationSettingsRepository,
        MerchantEmailNotificationSettingsRepository $merchantEmailNotificationSettingsRepository
    ) {
        $this->merchantRepo = $merchantRepository;
        $this->notificationSettingsRepo = $notificationSettingsRepository;
        $this->merchantEmailNotificationSettingsRepo = $merchantEmailNotificationSettingsRepository;

        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentMerchant = $this->merchantRepo->getCurrent();
            if (! $this->currentMerchant) {
                abort(401, 'Please add account (merchant)');
            }

            return $next($request);
        });
    }

    public function get($group, $type)
    {
        $group = str_replace('-', '_', $group);
        $type = str_replace('-', '_', $type);

        $allowed_types = NotificationSettings::AVAILABLE_TYPES;
        if (! isset($allowed_types[$group])) {
            abort(404);
        }
        if (! in_array($type, $allowed_types[$group])) {
            abort(404);
        }

        $notification_type = $group.'_'.$type;

        $emailNotification = $this->notificationSettingsRepo->findByType($this->currentMerchant, $notification_type);

        if (! $emailNotification) {
            return response()->json([], 201);
        }

        return fractal()->item($emailNotification)->transformWith(new NotificationSettingsTransformer)->toArray();
    }

    public function store(NotificationSettingsCreateRequest $request, $group, $type)
    {
        $data = $request->all();

        $group = str_replace('-', '_', $group);
        $type = str_replace('-', '_', $type);

        $allowed_types = NotificationSettings::AVAILABLE_TYPES;
        if (! isset($allowed_types[$group])) {
            abort(404);
        }
        if (! in_array($type, $allowed_types[$group])) {
            abort(404);
        }

        $notification_type = $group.'_'.$type;

        $data = array_merge($data, ['notification_type' => $notification_type]);

        if (isset($data['icons'])) {
            $data['icons'] = array_filter($data['icons'], function ($v, $k) use ($notification_type) {
                if ($this->checkIfIconAvailableInEmailNotifications($notification_type, $k)) {
                    if (preg_match('/rgba?\(([0-9]+)\, ([0-9]+)\, ([0-9]+)/', $v)){
                        return true;
                    }
                }
                return false;
            }, ARRAY_FILTER_USE_BOTH);
        }


        // @todo: Make customized icon generation
        /*$used_icons = [];
        if (isset($data['icons'])) {
            $icons = $data['icons'];
            foreach ($icons as $icon => $color) {
                $imageForIcon = app('email_notification')->getImageForIcon($notification_type, $icon);
                if ($imageForIcon && trim($imageForIcon)) {
                    $used_icons[$icon] = [
                        'img'   => $imageForIcon,
                        'color' => $color,
                    ];
                }
            }
            unset($data['icons']);
        }

        $icons = [];
        if (count($used_icons)) {
            foreach ($used_icons as $icon => $icon_data) {
                // Validate color
                if (preg_match('/rgba?\(([0-9]+)\, ([0-9]+)\, ([0-9]+)/', $icon_data['color'], $rgb_matches)) {
                    $r = $rgb_matches[1] >= 0 && $rgb_matches[1] <= 255 ? $rgb_matches[1] : 0;
                    $g = $rgb_matches[2] >= 0 && $rgb_matches[2] <= 255 ? $rgb_matches[2] : 0;
                    $b = $rgb_matches[3] >= 0 && $rgb_matches[3] <= 255 ? $rgb_matches[3] : 0;

                    // Colorize image
                    if (file_exists($icon_data['img'])) {
                        $img = $this->switchPngImageColor($icon_data['img'], $r, $g, $b);
                        if ($img) {
                            // Save to S3
                            $icon_name = $this->currentMerchant->id.'_'.time().'_'.md5(uniqid().'_'.rand());
                            $path = 'merchants/email-notification-icons/'.$icon_name;
                            try {
                                Storage::disk('s3')->put($path, $this->pngImageData($img), 'public');
                                $icon_url = $path;
                                // Push to data
                                $icons[$icon] = $icon_url;
                            } catch (\Exception $e) {

                            }
                        }
                    }
                }
            }
        }

        // Remove old icons
        $oldNotificationSetting = $this->notificationSettingsRepo->findByType($this->currentMerchant, $notification_type);
        if ($oldNotificationSetting) {
            $old_icons = $oldNotificationSetting->icons;
            if ($old_icons && count($old_icons)) {
                foreach ($old_icons as $icon => $path) {
                    try {
                        Storage::disk('s3')->delete($path);
                    } catch (\Exception $e) {

                    }
                }
            }
        }

        $data = array_merge($data, ['icons' => $icons]);*/

        $emailNotification = $this->notificationSettingsRepo->updateOrCreate($this->currentMerchant, $data);

        if (! $emailNotification) {
            return response()->json([
                'message' => 'Unexpected error occurred',
            ], 500);
        }

        return fractal()->item($emailNotification)->transformWith(new NotificationSettingsTransformer)->toArray();
    }

    protected function checkIfIconAvailableInEmailNotifications($type, $icon)
    {
        $icons = [
            'points_earned'            => [
                'reward-icon',
            ],
            'points_spent'             => [
                'reward-icon',
            ],
            'points_reward_available'  => [
                'reward-icon',
            ],
            'points_point_expiration'  => [],
            'points_vip_tier_earned'   => [
                'vip-tier-icon',
            ],
            'referral_share_email'     => [
                'reward-icon',
            ],
            'referral_receiver_reward' => [
                'reward-icon',
            ],
            'referral_sender_reward'   => [
                'reward-icon',
            ],
        ];

        return isset($icons[$type]) && in_array($icon, $icons[$type]) ? true : false;
    }

    protected function switchPngImageColor($path = null, $r = null, $g = null, $b = null)
    {
        try {
            $im = imagecreatefrompng($path);
            $w = imagesx($im);
            $h = imagesy($im);

            imagealphablending($im, false);
            imagesavealpha($im, true);

            for ($wi = 0; $wi < $w; $wi++) {
                for ($hi = 0; $hi < $h; $hi++) {
                    $rgb = imagecolorat($im, $wi, $hi);
                    $orig_r = ($rgb >> 16) & 0xFF;
                    $orig_g = ($rgb >> 8) & 0xFF;
                    $orig_b = $rgb & 0xFF;
                    $orig_a = ($rgb >> 24) & 0x7F;

                    $n = $orig_a;
                    $n = $n << 8 | (! is_null($r) ? $r : $orig_r);    // R
                    $n = $n << 8 | (! is_null($g) ? $g : $orig_g);    // G
                    $n = $n << 8 | (! is_null($b) ? $b : $orig_b);    // B

                    imagesetpixel($im, $wi, $hi, $n);
                }
            }

            return $im;
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function pngImageData($png)
    {
        ob_start();
        imagepng($png);

        return (ob_get_clean());
    }

    public function test(Request $request, $group, $type)
    {
        $request->validate([
            'to_email' => 'required|email',
            'to_name'  => 'string|max:191',
        ]);

        $group = str_replace('-', '_', $group);
        $type = str_replace('-', '_', $type);

        $notification_type = $group.'_'.$type;

        $to_name = $request->get('to_name') ? htmlspecialchars($request->get('to_name')) : '';
        $to_email = $request->get('to_email') ?: '';

        try {
            app('email_notification')->send($notification_type, $this->currentMerchant->id, $to_name, $to_email);
        } catch (EmailNotificationException $exception) {
            return response()->json([
                'message' => 'An error occurred while attempting to send email. '.$exception->getMessage(),
            ], 500);
        }
    }

    public function getSettings()
    {

        $item = $this->merchantEmailNotificationSettingsRepo->find($this->currentMerchant);

        if (! $item) {
            return response()->json([
                'data' => [],
            ], 201);
        }

        return fractal()
            ->item($item)
            ->parseIncludes('merchant')
            ->transformWith(new MerchantEmailNotificationSettingsTransformer)
            ->toArray();
    }

    public function saveSettings(SaveSettingsRequest $request)
    {
        $data = $request->all();

        $item = $this->merchantEmailNotificationSettingsRepo->updateOrCreate($this->currentMerchant, $data);

        return fractal()->item($item)->transformWith(new MerchantEmailNotificationSettingsTransformer)->toArray();
    }

    public function view()
    {
        $has_remove_branding_permissions = $this->merchantRepo->getCurrent()
            ->checkPermitionByTypeCode(config('permissions.typecode.RemoveLootlyBrandingEmail'));
        $branding_upsell = PaidPermission::getByTypeCode(config('permissions.typecode.RemoveLootlyBrandingEmail'));

        $have_domain_permissions = $this->merchantRepo->getCurrent()
            ->checkPermitionByTypeCode(config('permissions.typecode.CustomDomain'));
        $domain_upsell = PaidPermission::getByTypeCode(config('permissions.typecode.CustomDomain'));

        return view('display.email.settings', compact('has_remove_branding_permissions', 'branding_upsell', 'have_domain_permissions', 'domain_upsell'));
    }
}