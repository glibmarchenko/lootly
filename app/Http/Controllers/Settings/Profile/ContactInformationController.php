<?php

namespace App\Http\Controllers\Settings\Profile;

use App\Repositories\UserNotificationTypeRepository;
use Illuminate\Http\Request;
use Laravel\Spark\Contracts\Interactions\Settings\Profile\UpdateContactInformation;
use App\Models\PaidPermission;
use App\Repositories\CurrencyRepository;
use App\Repositories\InvitationRepository;
use App\Repositories\LanguageRepository;
use App\Repositories\MerchantRepository;
use App\Repositories\UserRepository;

class ContactInformationController extends \Laravel\Spark\Http\Controllers\Settings\Profile\ContactInformationController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $currencyModel;
    protected $languageModel;
    protected $userNotificationTypeModel;

    public function __construct(CurrencyRepository $currencyRepository, MerchantRepository $merchantRepository,
                                LanguageRepository $languageRepository, InvitationRepository $invitationRepository,
                                UserRepository $userRepository)
    {
        $this->currencyModel = new CurrencyRepository();
        $this->languageModel = new LanguageRepository();
        $this->userNotificationTypeModel = new UserNotificationTypeRepository();


        $this->currencyRepository = $currencyRepository;
        $this->userRepository = $userRepository;
        $this->merchantRepository = $merchantRepository;
        $this->languageRepository = $languageRepository;
        $this->invitationRepository = $invitationRepository;

        $this->middleware('auth');
    }

    public function settings()
    {
        $currencies = $this->currencyModel->get();
        $languages = $this->languageModel->get();
        $user_notification_types = $this->userNotificationTypeModel->get();
        $has_employee_access_permissions = $this->merchantRepository
            ->getCurrent()
            ->checkPermitionByTypeCode(\Config::get('permissions.typecode.EmployeeAccess'));
        $upsell = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.EmployeeAccess'));
        return view('account.settings', compact('currencies', 'languages', 'user_notification_types', 'upsell', 'has_employee_access_permissions'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSettings()
    {
        $currencies = $this->currencyRepository->get();
        $user = $this->merchantRepository->getCurrent();
        $languages = $this->languageRepository->get();
        if (!$user) {
            $user = \Auth::user();
        }
        return response()->json([
            'merchant' => $user,
            'currencies' => $currencies,
            'languages' => $languages,
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function settingEmployee()
    {
        $merchantObj = $this->merchantRepository->getCurrent();

        $employeeArr = $this->invitationRepository->get($merchantObj);
        return response()->json([
            'employees' => $employeeArr

        ]);
    }

    /**
     * Update the user's contact information settings.
     *
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        // Edit Account information
        $this->interaction(
            $request, UpdateContactInformation::class,
            [$request->user(), $request->all()]
        );
        // Edit Store
        $request->validate([
            'name' => 'required|max:191',
            'currency_id' => 'required|integer|exists:currencies,id',
            'currency_display_sign' => 'required|boolean',
            'language' => 'required|max:191',
            'customer_earned_point_notification' => 'boolean',
            'customer_spent_point_notification' => 'boolean',
        ]);
        $data = $request->all();
        $merchant = $this->merchantRepository->update($data);

        //Edit Notification
        $this->userRepository->updateNotification($data);

        return response()->json([
            'merchant' => $merchant
        ]);
    }
}
