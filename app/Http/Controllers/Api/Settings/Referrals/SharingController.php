<?php

namespace App\Http\Controllers\Api\Settings\Referrals;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Referrals\Sharing\StoreRequest;
use App\Merchant;
use App\Repositories\Contracts\ReferralSharingRepository;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Services\Amazon\UploadFile;
use App\Transformers\ReferralSharingTransformer;
use Illuminate\Http\Request;

class SharingController extends Controller
{
    protected $referralSharing;

    public function __construct(ReferralSharingRepository $referralSharing)
    {
        $this->referralSharing = $referralSharing;
    }

    public function get(Request $request, Merchant $merchant)
    {
        try {
            $sharingSettings = $this->referralSharing->withCriteria([
                new LatestFirst(),
            ])->findWhereFirst([
                'merchant_id' => $merchant->id,
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
                    'email_body'    => '{receiver-name}, '."\n".'{sender-name} just sent you a {reward-name} coupon for your next order at {company}.',

                ],
            ]);
        }
    }

    public function store(StoreRequest $request, Merchant $merchant)
    {
        $amazon = new UploadFile();

        $data = [
            'facebook_status' => $request->get('facebook')['status'],
            'twitter_status'  => $request->get('twitter')['status'],
            'google_status'   => $request->get('google')['status'],
            'email_status'    => $request->get('email')['status'],
        ];

        if ($request->get('title')) {
            $data['share_title'] = trim($request->get('title'));
        }

        if ($request->get('description')) {
            $data['share_description'] = trim($request->get('description'));
        }

        if ($request->get('facebook')) {
            $facebook_data = $request->get('facebook');
            if (isset($facebook_data['message'])) {
                $data['facebook_message'] = trim($facebook_data['message']);
            }

            if (isset($facebook_data['icon']) && $facebook_data['icon'] && $facebook_data['icon'] != $facebook_data['old_icon']) {
                $data['facebook_icon_name'] = $facebook_data['icon_name'];
                $data['facebook_icon'] = $amazon->upload($merchant, $facebook_data['icon'], null);
                $this->deleteImage($facebook_data['old_icon']);                
            } else if(!isset($facebook_data['icon']) || !$facebook_data['icon']) {
                $data['facebook_icon'] = '';
                $data['facebook_icon_name'] = '';
                $this->deleteImage($facebook_data['old_icon']);
            }
        }

        if ($request->get('twitter')) {
            $twitter_data = $request->get('twitter');
            if (isset($twitter_data['message'])) {
                $data['twitter_message'] = trim($twitter_data['message']);
            }
            if (isset($twitter_data['icon']) && trim($twitter_data['icon']) && $twitter_data['icon'] != $twitter_data['old_icon']) {
                $data['twitter_icon_name'] = $twitter_data['icon_name'];                
                $data['twitter_icon'] = $amazon->upload($merchant, $twitter_data['icon'], null);
            }
            if($twitter_data['old_icon'] != '' && $twitter_data['old_icon'] != $twitter_data['icon']) {
                $data['twitter_icon'] = '';
                $data['twitter_icon_name'] = '';
                $this->deleteImage($twitter_data['old_icon']);
            }

            if (isset($twitter_data['icon']) && $twitter_data['icon'] && $twitter_data['icon'] != $twitter_data['old_icon']) {
                $data['twitter_icon_name'] = $twitter_data['icon_name'];
                $data['twitter_icon'] = $amazon->upload($merchant, $twitter_data['icon'], null);
                $this->deleteImage($twitter_data['old_icon']);                
            } else if(!isset($twitter_data['icon']) || !$twitter_data['icon']) {
                $data['twitter_icon'] = '';
                $data['twitter_icon_name'] = '';
                $this->deleteImage($twitter_data['old_icon']);
            }

        }

        if ($request->get('email')) {
            $email_data = $request->get('email');
            if (isset($email_data['subject'])) {
                $data['email_subject'] = trim($email_data['subject']);
            }
            if (isset($email_data['body'])) {
                $data['email_body'] = trim($email_data['body']);
            }
        }

        $sharingSettings = $this->referralSharing->updateOrCreate([
            'merchant_id' => $merchant->id,
        ], $data);

        return $sharingSettings;
    }

    private function deleteImage($image = null)
    {
        if ($image) {
            $amazon = new UploadFile();

            $split_path = explode('/', $image);
            $index = count($split_path);
            $path = $split_path[$index - 1];

            $amazon->delete($path);
        }
    }

}