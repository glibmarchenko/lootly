<?php

namespace App\Http\Controllers\Settings\Vip;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\CustomerRepository;
use App\Repositories\MerchantRepository;
use App\Repositories\TierRepository;
use Maatwebsite\Excel\Facades\Excel;
use App\Repositories\Contracts\TierHistoryRepository;
use App\Repositories\Eloquent\Criteria\ByCustomer;
use App\Transformers\VipActivitiesTransformer;
use App\Transformers\VipActivityExportTransformer;

class  ActivityController extends Controller
{
    /**
     * ActivityController constructor.
     * @param TierRepository $tierRepository
     * @param MerchantRepository $merchantRepository
     */
    public function __construct(
        CustomerRepository $customerRepository,
        MerchantRepository $merchantRepository,
        TierHistoryRepository $tierHistoryRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->merchantRepository = $merchantRepository;
        $this->tierHistoryRepository = $tierHistoryRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show()
    {
        $merchant = $this->merchantRepository->getCurrent();
        $interval = new \DateInterval("P".env('DefaultReportingPeriod', 30)."D");
        $dateTime = new \DateTime;
        $start = $dateTime->modify('today') //get start of this day
            ->add(new \DateInterval("P1D")) //set to end of the day
            ->sub($interval);
        $period = new \DatePeriod($start, $interval, 1);

        $activities = fractal()->collection(
                $this->tierHistoryRepository->getByPeriod($period, $merchant, ['customer', 'new_tier', 'old_tier'])
            )
            ->transformWith(new VipActivitiesTransformer())
            ->toArray()['data'];
        // dd($activities);
        return view('vip.activity', compact('activities'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request)
    {
        $merchant = $this->merchantRepository->getCurrent();
        $start = new \DateTime($request->get('start'));
        $end = new \DateTime($request->get('end'));
        $period = new \DatePeriod($start, $start->diff($end), 1);
        $activities = fractal()->collection($this->tierHistoryRepository->getByPeriod($period, $merchant, ['customer', 'new_tier', 'old_tier']))
            ->transformWith(new VipActivitiesTransformer())
            ->toArray()['data'];
        return response()->json([
            'activities' => $activities
        ]);
    }

    /**
     * @return mixed
     */
    public function export(Request $request)
    {
        $merchant = $this->merchantRepository->getCurrent();
        $start = $request->get('start');
        $end = $request->get('end');
        $search = $request->get('search');

        $activities = $this->tierHistoryRepository->getByMerchant($merchant->id)->sortByDesc('created_at');
        if(isset($start) && isset($end)){
            $startDate = new \DateTime($start);
            $endDate = new \DateTime($end);
            $activities = $activities
                ->where('created_at', '>', $startDate)
                ->where('created_at', '<', $endDate);
        }

        if(isset($search) && !empty($search)){
            $activities = $activities->filter(function($item) use ($search){
                return stripos($item->activity, $search) !== false || stripos($item->customer->name, $search) !== false;
            });
        }

        $data = fractal()->collection($activities)
            ->parseIncludes(['new_tier', 'customer', 'old_tier'])
            ->transformWith(new VipActivityExportTransformer())
            ->toArray()['data'];

        return Excel::create('ActivityVip', function ($excel) use ($data) {
            $excel->setTitle('Customer Export');
            $excel->sheet('Excel sheet', function ($sheet) use ($data) {
                $sheet->setOrientation('landscape');
                $sheet->fromArray($data);
            });
        })->export('csv');
    }

    /**
     * @param $activityVips
     * @return \Illuminate\Support\Collection
     */
    public function generateActivityResponse($activityVips)
    {
        $activities = collect();
        foreach ($activityVips as $activityVip) {
            $activityData = [
                'name' => $activityVip->name,
                'activity' => '',
                'current_tier' => (isset($activityVip->tier[0])) ? $activityVip->tier[0]->name : '',
                'previous_tier' => '',
                'date' => $activityVip->created_at->diffForHumans(),
            ];
            $activities->push($activityData);
        }
        return $activities;
    }
}