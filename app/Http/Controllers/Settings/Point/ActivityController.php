<?php

namespace App\Http\Controllers\Settings\Point;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\CustomerRepository;
use App\Repositories\MerchantRepository;
use App\Repositories\PointRepository;
use App\Repositories\ReportsRepository;
use Maatwebsite\Excel\Facades\Excel;
use App\Repositories\Eloquent\Criteria\Limit;
use App\Repositories\Eloquent\Criteria\Offset;
use App\Repositories\Eloquent\Criteria\OrderBy;
use App\Repositories\Eloquent\Criteria\ByMerchant;
use App\Repositories\Eloquent\Criteria\SearchByAll;
use App\Repositories\Eloquent\Criteria\BetweenDates;
use App\Repositories\Eloquent\Criteria\SelectByFields;
use App\Repositories\Eloquent\Criteria\WithActionName;
use App\Repositories\Eloquent\Criteria\GetByActionType;
use App\Repositories\Eloquent\Criteria\WithCustomerName;
use App\Repositories\Eloquent\Criteria\AdminAdjustPoints;
use App\Transformers\PointsActivityViewTransformer;
use App\Transformers\PointsActivityExportTransformer;


class ActivityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PointRepository $pointRepositoryContract, MerchantRepository $merchantRepository,
                                CustomerRepository $customerRepository)
    {
        $this->pointRepository = $pointRepositoryContract;
        $this->merchantRepository = $merchantRepository;
        $this->customerRepository = $customerRepository;
        $this->reportsRepository = new ReportsRepository;
        $this->middleware('auth');
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPointActivity()
    {
        $currentMerchantObj = $this->merchantRepository->getCurrent();

        $pointActivityObj = $this->customerRepository->getPoints($currentMerchantObj);


        return response()->json([
            'pointActivity' => $pointActivityObj
        ]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function export(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');
        $actionName = $request->get('action');
        $pointType = $request->get('point');
        $search = $request->get('search');
        $merchant = $this->merchantRepository->getCurrent();

        $criteries = [
            new SelectByFields,
            new ByMerchant($merchant->id),
            new WithCustomerName(),
            new OrderBy('created_at', 'desc'),
        ];

        if (!empty($start) && !empty($end) && empty($search)) {
            $criteries[] = new BetweenDates($start, $end);
        }

        if ($pointType != 'All') {
            $criteries[] = new GetByActionType($pointType);
        }

        $search = $request->get('search');
        $columnsToSearch = ['title', 'customer.name'];
        if (!empty($search) && strlen($search) > 2) {
            $criteries[] = new SearchByAll($search, $columnsToSearch);
        }

        if ($actionName != 'All') {
            if ($actionName != 'Admin') {
                $criteries[] = new WithActionName($actionName, $merchant->id);
            } else {
                $criteries[] = new AdminAdjustPoints();
            }
        }

        $activities = $this->pointRepository->getPointsByCriteries($criteries)->all();
        $activitiesData = fractal($activities)->transformWith(new PointsActivityExportTransformer())->toArray()['data'];
        return Excel::create('Activity', function ($excel) use ($activitiesData) {
            $excel->setTitle('Customer Export');
            $excel->sheet('Excel sheet', function ($sheet) use ($activitiesData) {
                $sheet->setOrientation('landscape');
                $sheet->fromArray($activitiesData);
            });
        })->export('csv');
    }

    public function getActivity(Request $request){

        $merchant = $this->merchantRepository->getCurrent();

        $criteries = [
            new SelectByFields,
            new ByMerchant($merchant->id),
            new WithCustomerName(),
            new Limit($request->get('limit')),
            new Offset($request->get('offset')),
        ];

        $criteriesForCount = [
            new ByMerchant($merchant->id)
        ];

        $actionType = $request->get('type');
        if($actionType != 'All') {
            \array_push($criteries, new GetByActionType($actionType));
            \array_push($criteriesForCount, new GetByActionType($actionType));
        }

        $actionName = $request->get('action');
        if($actionName != 'All') {
            if($actionName != 'Admin') {
                \array_push($criteries, new WithActionName($actionName, $merchant->id));
                \array_push($criteriesForCount, new WithActionName($actionName, $merchant->id));
                \array_push($criteries, new OrderBy($request->get('sort_by'), $request->get('sort_dir')));
            } else {
                \array_push($criteries, new AdminAdjustPoints);
                \array_push($criteriesForCount, new AdminAdjustPoints);
                \array_push($criteries, new OrderBy('type', $request->get('sort_dir')));
            }
        } else {
            \array_push($criteries, new OrderBy($request->get('sort_by'), $request->get('sort_dir')));
        }

        $search = $request->get('search');
        $columnsToSearch = ['title', 'customer.name'];
        if(!empty($search)) {
            if(strlen($search) > 2) {
                \array_push($criteries, new SearchByAll($search, $columnsToSearch));
                \array_push($criteriesForCount, new SearchByAll($search, $columnsToSearch));
            }
        }

        $start = $request->get('start');
        $end = $request->get('end');
        if(!empty($start) && !empty($end) && empty($search)) {
            \array_push($criteries, new BetweenDates($start, $end));
            \array_push($criteriesForCount, new BetweenDates($start, $end));
        }

        $actions = $this->pointRepository->getPointsByCriteries($criteries)->all();
        $countAction = $this->pointRepository->getPointsByCriteries($criteriesForCount)->count();
        $formatedActions = fractal()->collection($actions)
            ->transformWith(new PointsActivityViewTransformer)
            ->toArray()['data'];

        return response()->json([ 'actions' => $formatedActions, 'total' => $countAction]);
    }
}
