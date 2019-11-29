<?php

namespace App\Http\Controllers\Settings\referrals;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\Referral\EditRequest;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Repositories\Eloquent\Criteria\SearchByAll;
use App\Repositories\MerchantRepository;
use App\Repositories\Contracts\MerchantRepository as Merchants;
use App\Repositories\ReferralSettingsRepository;
use App\Models\PaidPermission;
use App\Transformers\ReferralsActivityExportTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SettingsReferralsController extends Controller
{
    protected $referralSettingsRepository;

    protected $merchantRepository;

    protected $merchants;

    /**
     * Create a new controller instance.
     *
     * @param ReferralSettingsRepository $referralSettingsRepository
     * @param MerchantRepository $merchantRepository
     * @param Merchants $merchants
     */
    public function __construct(
        ReferralSettingsRepository $referralSettingsRepository,
        MerchantRepository $merchantRepository,
        Merchants $merchants
    )
    {
        $this->referralSettingsRepository = $referralSettingsRepository;
        $this->merchantRepository = $merchantRepository;
        $this->merchants = $merchants;
    }

    /**
     * @param EditRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateReferral(EditRequest $request)
    {
        $merchantObj = $this->merchantRepository->getCurrent();
        $data = $request->all();
        $response = $this->referralSettingsRepository->updateReferral($data, $merchantObj);
        return \response()->json([
            'response' => $response,
            'message' => 'Success referral settings'
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReferral()
    {
        $merchantObj = $this->merchantRepository->getCurrent();
        $referral = $this->referralSettingsRepository->getReferral($merchantObj);

        return response()->json([
            'referral' => $referral,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function export(Request $request)
    {

        $merchant = $this->merchantRepository->getCurrent();

        $start = $request->get('start');
        $end = $request->get('end');
        $search = $request->get('search');

        $criteria = [
            new LatestFirst(),
            new EagerLoad([
                'customer',
                'referral',
            ]),
        ];

        $columnsToSearch = ['customer.name', 'referral.name'];
        if (!empty($search) && strlen($search) > 2) {
            $criteria[] = new SearchByAll($search, $columnsToSearch);
        }

        $activities = $this->merchants->referredOrders($merchant->id)->withCriteria($criteria);
        if (!empty($start) && !empty($end)) {
            $startDate = new \DateTime($start);
            $endDate = new \DateTime($end);
            $activities = $activities
                ->findWhere([
                    ['orders.created_at', '>', Carbon::instance($startDate)],
                    ['orders.created_at', '<', Carbon::instance($endDate)]
                ]);
        } else {
            $activities = $activities->all();
        }
        $activitiesData = fractal($activities)->transformWith(new ReferralsActivityExportTransformer())->toArray()['data'];
        return Excel::create('Referrals-activity', function ($excel) use ($activitiesData) {
            $excel->setTitle('Customer Export');
            $excel->sheet('Excel sheet', function ($sheet) use ($activitiesData) {
                $sheet->setOrientation('landscape');
                $sheet->fromArray($activitiesData);
            });
        })->export('csv');
    }

    public function view()
    {
        $data['have_domain_permissions'] = $this->merchantRepository
            ->getCurrent()
            ->checkPermitionByTypeCode(\Config::get('permissions.typecode.CustomDomain'));
        $data['domain_upsell'] = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.CustomDomain'));

        return view('referrals.settings', $data);
    }
}
