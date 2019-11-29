<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\UpdateUserSettingsRequest;
use App\Repositories\Contracts\MerchantDetailsRepository;
use App\Repositories\MerchantRepository;
use App\Repositories\UserNotificationTypeRepository;
use App\Repositories\UserRepository;
use App\Transformers\UserSettingsTransformer;
use App\Http\Controllers\Settings\Profile\UpdateCurrencyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSettingsController extends Controller
{
    protected $users;

    protected $merchantDetails;

    protected $userModel;

    protected $merchantModel;

    protected $userNotificationTypeModel;

    /**
     * UserSettingsController constructor.
     *
     * @param \App\Repositories\Contracts\UserRepository            $users
     * @param \App\Repositories\Contracts\MerchantDetailsRepository $merchantDetails
     */
    public function __construct(
        \App\Repositories\Contracts\UserRepository $users,
        MerchantDetailsRepository $merchantDetails
    ) {
        $this->users = $users;
        $this->merchantDetails = $merchantDetails;

        $this->userModel = new UserRepository();
        $this->merchantModel = new MerchantRepository();
        $this->userNotificationTypeModel = new UserNotificationTypeRepository();
        $this->updateCurrencyController = new UpdateCurrencyController();
    }

    public function get(Request $request)
    {
        $user = $this->users->current();

        return fractal($user)->parseIncludes([
            //'current_merchant',
            'notifications',
        ])->transformWith(new UserSettingsTransformer)->toArray();
    }

    public function update(UpdateUserSettingsRequest $request)
    {
        $requestData = $request->all();
        try {
            $this->userModel->update($request->user(), $requestData['user']);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->getCode());
        }
        $exeptions = [];
        if (\Spark::usesTeams()) {
            if (isset($requestData['merchant'])) {
                $request_merchant = $requestData['merchant'];
                if (isset($request_merchant['id']) && $request->user()->hasTeams()) {
                    $merchant = $this->merchantModel->find($request_merchant['id']);
                    if ($request->user()->onTeam($merchant) && $request->user()->roleOn($merchant) === 'owner') {
                        $request->validate([
                            'merchant.name'                  => 'required|max:191',
                            'merchant.currency_id'           => 'nullable|integer|exists:currencies,id',
                            'merchant.currency_display_sign' => 'nullable|boolean',
                            'merchant.language'              => 'required|max:191',
                        ]);
                        $data = $request->get('merchant');
                        try {
                            $this->merchantModel->update($merchant, $data);
                            $this->updateCurrencyController->updateVipCurrency($merchant);
                        } catch (\Exception $exception) {
                            \Log::error($exception);
                            return response()->json([
                                'message' => $exception->getMessage(),
                            ], 501);
                        }
                        if (isset($request_merchant['api'])) {
                            $updateApiKeysData = [];
                            if (isset($request_merchant['api']['key']) && isset($request_merchant['api']['key_hash'])) {
                                $hash = base64_encode(hash_hmac('sha256', $request_merchant['api']['key'], config('app.key'), true));
                                if ($hash == $request_merchant['api']['key_hash']) {
                                    $updateApiKeysData['api_key'] = $request_merchant['api']['key'];
                                }
                            }
                            if (isset($request_merchant['api']['secret']) && isset($request_merchant['api']['secret_hash'])) {
                                $hash = base64_encode(hash_hmac('sha256', $request_merchant['api']['secret'], config('app.key'), true));
                                if ($hash == $request_merchant['api']['secret_hash']) {
                                    $updateApiKeysData['api_secret'] = $request_merchant['api']['secret'];
                                }
                            }
                            if (count($updateApiKeysData)) {
                                try {
                                    $this->merchantDetails->updateOrCreate(['merchant_id' => $merchant->id], $updateApiKeysData);
                                } catch (\Exception $e) {
                                    \array_push($exeptions, $e->getMessage());
                                }
                            }
                        }
                    }
                }
            }
        }

        $notifications = $requestData['user']['notifications'] ?? [];
        $availableNotificationTypes = $this->userNotificationTypeModel->getSlugList();
        if (count($notifications)) {
            foreach ($notifications as $key => $value) {
                if (isset($availableNotificationTypes[$key])) {
                    $this->userModel->updateNotificationSettings(Auth::user(), $availableNotificationTypes[$key], $value);
                }
            }
        }
        return \response()->json(['exeptions' => $exeptions], 200);
    }
}