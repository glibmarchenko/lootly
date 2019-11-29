<?php

namespace App\Helpers;

use App\Models\MerchantReward;
use App\Repositories\Contracts\NotificationSettingsRepository;
use App\Repositories\Eloquent\Criteria\ByMerchant;
use Illuminate\Support\Facades\Storage;

class RewardService
{
    protected $notificationSettings;

    /**
     * RewardService constructor.
     *
     * @param \App\Repositories\Contracts\NotificationSettingsRepository $notificationSettings
     */
    public function __construct(NotificationSettingsRepository $notificationSettings)
    {
        $this->notificationSettings = $notificationSettings;
    }

    public function getRewardIconUrl(MerchantReward $merchantReward, $iconTag = null, $notificationType = null)
    {
        if (trim($merchantReward->reward_icon)) {
            return $merchantReward->reward_icon;
        }

        if (! trim($merchantReward->reward_icon_name)) {
            return null;
        }

        $iconName = $merchantReward->reward_icon_name;
        $iconNameParts = explode('-', $iconName);
        if (count($iconNameParts)) {
            $iconName = end($iconNameParts);
        }

        if (! file_exists(public_path().'/images/icons/lootly/'.$iconName.'.png')) {
            return null;
        }

        $rewardIconPngRelativePath = '/images/icons/lootly/'.$iconName.'.png';

        $rewardIconPngPath = public_path().$rewardIconPngRelativePath;

        if (! $iconTag) {
            return url($rewardIconPngRelativePath);
        }

        if (! $notificationType) {
            return url($rewardIconPngRelativePath);
        }

        try {
            $notificationSettings = $this->notificationSettings->withCriteria([
                new ByMerchant($merchantReward->merchant_id),
            ])->findWhereFirst([
                'notification_type' => $notificationType,
            ]);

            $iconSettings = $notificationSettings->icons;

            if (! isset($iconSettings[$iconTag]) || ! trim($iconSettings[$iconTag])) {
                return url($rewardIconPngRelativePath);
            }

            $iconColor = $iconSettings[$iconTag];

            // Validate color
            if (preg_match('/rgba?\(([0-9]+)\, ([0-9]+)\, ([0-9]+)/', $iconColor, $rgb_matches)) {
                $r = $rgb_matches[1] >= 0 && $rgb_matches[1] <= 255 ? $rgb_matches[1] : 0;
                $g = $rgb_matches[2] >= 0 && $rgb_matches[2] <= 255 ? $rgb_matches[2] : 0;
                $b = $rgb_matches[3] >= 0 && $rgb_matches[3] <= 255 ? $rgb_matches[3] : 0;

                $availableColors = [
                    [
                        "255",
                        "255",
                        "255",
                    ],
                    [
                        "0",
                        "0",
                        "0",
                    ],
                    [
                        "238",
                        "236",
                        "225",
                    ],
                    [
                        "31",
                        "73",
                        "125",
                    ],
                    [
                        "79",
                        "129",
                        "189",
                    ],
                    [
                        "192",
                        "80",
                        "77",
                    ],
                    [
                        "155",
                        "187",
                        "89",
                    ],
                    [
                        "128",
                        "100",
                        "162",
                    ],
                    [
                        "75",
                        "172",
                        "198",
                    ],
                    [
                        "247",
                        "150",
                        "70",
                    ],
                    [
                        "255",
                        "255",
                        "0",
                    ],
                    [
                        "242",
                        "242",
                        "242",
                    ],
                    [
                        "127",
                        "127",
                        "127",
                    ],
                    [
                        "221",
                        "217",
                        "195",
                    ],
                    [
                        "198",
                        "217",
                        "240",
                    ],
                    [
                        "219",
                        "229",
                        "241",
                    ],
                    [
                        "242",
                        "220",
                        "219",
                    ],
                    [
                        "235",
                        "241",
                        "221",
                    ],
                    [
                        "229",
                        "224",
                        "236",
                    ],
                    [
                        "219",
                        "238",
                        "243",
                    ],
                    [
                        "253",
                        "234",
                        "218",
                    ],
                    [
                        "255",
                        "242",
                        "202",
                    ],
                    [
                        "216",
                        "216",
                        "216",
                    ],
                    [
                        "89",
                        "89",
                        "89",
                    ],
                    [
                        "196",
                        "189",
                        "151",
                    ],
                    [
                        "141",
                        "179",
                        "226",
                    ],
                    [
                        "184",
                        "204",
                        "228",
                    ],
                    [
                        "229",
                        "185",
                        "183",
                    ],
                    [
                        "215",
                        "227",
                        "188",
                    ],
                    [
                        "204",
                        "193",
                        "217",
                    ],
                    [
                        "183",
                        "221",
                        "232",
                    ],
                    [
                        "251",
                        "213",
                        "181",
                    ],
                    [
                        "255",
                        "230",
                        "148",
                    ],
                    [
                        "191",
                        "191",
                        "191",
                    ],
                    [
                        "63",
                        "63",
                        "63",
                    ],
                    [
                        "147",
                        "137",
                        "83",
                    ],
                    [
                        "84",
                        "141",
                        "212",
                    ],
                    [
                        "149",
                        "179",
                        "215",
                    ],
                    [
                        "217",
                        "150",
                        "148",
                    ],
                    [
                        "195",
                        "214",
                        "155",
                    ],
                    [
                        "178",
                        "162",
                        "199",
                    ],
                    [
                        "183",
                        "221",
                        "232",
                    ],
                    [
                        "250",
                        "192",
                        "143",
                    ],
                    [
                        "242",
                        "195",
                        "20",
                    ],
                    [
                        "165",
                        "165",
                        "165",
                    ],
                    [
                        "38",
                        "38",
                        "38",
                    ],
                    [
                        "73",
                        "68",
                        "41",
                    ],
                    [
                        "23",
                        "54",
                        "93",
                    ],
                    [
                        "54",
                        "96",
                        "146",
                    ],
                    [
                        "149",
                        "55",
                        "52",
                    ],
                    [
                        "118",
                        "146",
                        "60",
                    ],
                    [
                        "95",
                        "73",
                        "122",
                    ],
                    [
                        "146",
                        "205",
                        "220",
                    ],
                    [
                        "227",
                        "108",
                        "9",
                    ],
                    [
                        "192",
                        "145",
                        "0",
                    ],
                    [
                        "127",
                        "127",
                        "127",
                    ],
                    [
                        "12",
                        "12",
                        "12",
                    ],
                    [
                        "29",
                        "27",
                        "16",
                    ],
                    [
                        "15",
                        "36",
                        "62",
                    ],
                    [
                        "36",
                        "64",
                        "97",
                    ],
                    [
                        "99",
                        "36",
                        "35",
                    ],
                    [
                        "79",
                        "97",
                        "40",
                    ],
                    [
                        "63",
                        "49",
                        "81",
                    ],
                    [
                        "49",
                        "133",
                        "155",
                    ],
                    [
                        "151",
                        "72",
                        "6",
                    ],
                    [
                        "127",
                        "96",
                        "0",
                    ],
                ];

                try {
                    $bestMetchingColor = $this->getBestMatchingColor([
                        $r,
                        $g,
                        $b,
                    ], $availableColors);
                    $r = $bestMetchingColor[0];
                    $g = $bestMetchingColor[1];
                    $b = $bestMetchingColor[2];
                } catch (\Exception $e) {
                    $r = 0;
                    $g = 0;
                    $b = 0;
                }

                $path = 'images/icons/'.$iconName.'_'.$r.'_'.$g.'_'.$b;

                // Check if icon exists on S3 already
                if (Storage::disk('s3')->exists($path)) {
                    return Storage::disk('s3')->url($path);
                }

                // Make customized icon generation if no icon on S3

                // Colorize image
                $img = $this->switchPngImageColor($rewardIconPngPath, $r, $g, $b);
                if ($img) {
                    // Save to S3
                    try {
                        Storage::disk('s3')->put($path, $this->pngImageData($img), 'public');

                        return Storage::disk('s3')->url($path);
                    } catch (\Exception $e) {

                    }
                }
            }
        } catch (\Exception $e) {
            // No notification settings found
        }

        return url($rewardIconPngRelativePath);
    }

    protected function getBestMatchingColor(
        array $color = [
            0,
            0,
            0,
        ],
        array $colors = []
    ) {
        $output = [
            0,
            0,
            0,
        ];
        $v = null;
        for ($i = 0; $i < count($colors); $i++) {
            $v2 = pow(($color[0] - $colors[$i][0]), 2) + pow(($color[1] - $colors[$i][1]), 2) + pow(($color[2] - $colors[$i][2]), 2);
            if ($v2 === 0) {
                $output = $colors[$i];
                $v = $v2;
                break;
            }
            if (is_null($v)) {
                $v = $v2;
                $output = $colors[$i];
            } else {
                if ($v2 < $v) {
                    $v = $v2;
                    $output = $colors[$i];
                }
            }
        }

        return $output;
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
}