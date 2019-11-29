<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\MerchantAction;
use App\Models\Point;
use App\Contracts\Repositories\PointRepository as PointRepositoryContract;
use App\Repositories\Contracts\PointRepository as PointRepositoryEloquent;
use Carbon\Carbon;

class PointRepository implements PointRepositoryContract
{
    public $baseQuery;

    protected $model;

    public function __construct(PointRepositoryEloquent $pointRepo)
    {
        $this->baseQuery = Point::query();
        $this->eloquentQuery = $pointRepo;
        $merchantRepository = new MerchantRepository();
        $this->merchantRepository = $merchantRepository;
    }

    public function find($id)
    {
        // TODO: Implement find() method.
    }

    /**
     * Create new Points
     *
     * @param     $order_data
     * @param int $point
     *
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    //    public function create($order_data, $point = 1)
    //    {
    //        $date = $order_data->customer->created_at;
    //        $date = date_format(date_create($date), 'Y-m-d');
    //        $newDate = Carbon::createFromFormat('Y-m-d', $date)->addYear(1);
    //        $merchant_id = Merchant::query()->where('location_id', '=', $order_data->location_id)->first()->id;
    //        $points = Point::query()->create([
    //            'customer_id' => $order_data->customer->id,
    //            'merchant_id' => $merchant_id,
    //            'point_value' => $point,
    //            'total_order_amount' => $order_data->total_price,
    //            'order_id' => $order_data->customer->last_order_id,
    //            'coupon_id' => $order_data->customer->last_order_id,
    //            'type' => $order_data->processing_method,
    //            'expiration_date' => $newDate,
    //        ]);
    //        return $points;
    //    }

    /**
     * Add points (+1)
     *
     * @param $merchant_id
     * @param $customer_id
     *
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function add($merchantObj, $customer_id)
    {
        //        $point = Customer::query()
        //            ->where('.customers.id', '=', $customer_id)
        //            ->where('.customers.merchant_id', '=', $merchantObj->id)
        //            ->select('points.*')
        //            ->join('points', 'points.customer_id', '=', 'customers.id')
        //            ->get()->toArray();
        //        $point['point_value'] = 1;
        //        $new_point = Point::query()->create($point);
        //
        //        return $new_point;

    }

    public function addPointsForAction(Customer $customer, MerchantAction $merchantAction, $data = [])
    {
        if (! isset($data['point_value']) || ! trim($data['point_value'])) {

        }

        $point = Point::make();
        $point->merchant_id = $merchantAction->merchant_id;
        $point->customer_id = $customer->id;
        $point->merchant_action_id = $merchantAction->id;
        $point->fill($data);
        $point->save();

        return $point;
    }

    public function update($user, $points)
    {
        // TODO: Implement update() method.
    }

    public function delete($user)
    {
        // TODO: Implement delete() method.
    }

    public function getPoint($price)
    {
        return ($price * 10) / 100;
    }

    public function create($data, $customerObj, $point = null, $type = 'Admin')
    {
        if (!$point) {
            if (isset($data['price'])) {
                $point = $this->getPoint($data['price']);
            } else {
                $point = 0;
            }
        }

        $this->baseQuery->create([
            'merchant_id' => $customerObj->merchant_id,
            'customer_id' => $customerObj->id,
            'point_value' => $point,
            'title'       => isset($data['title']) ? $data['title'] : '',
            'reason'      => isset($data['reason']) ? $data['reason'] : '',
            'type'        => $type
        ]);
    }

    public function adjust($requestObj, array $data)
    {

        $merchantObj = $this->merchantRepository->getCurrent();

        foreach ($data as $key => $row) {

            $pointsData = [
                'email'  => $row['email'],
                'points' => $row['points'],
                'title'  => $requestObj->title,
                'reason' => $requestObj->reason,
            ];

            $customerRepository = new CustomerRepository($this->merchantRepository);

            $customerObj = $customerRepository->getByEmail($pointsData['email']);

            if ($customerObj) {
                $this->create($pointsData, $customerObj, $pointsData['points'], 'Admin');
            }
        }

        return response()->json([
            'success' => 'Successfully Points Import',
        ]);
    }

    public function getLatestActivity($merchant) {
        if(empty($merchant)){
            return null;
        }

        return $this->baseQuery->where('merchant_id', '=', $merchant->id)
            ->orderBy('created_at', 'desc')->limit(5)->get();
    }

    /**
     * Select points with criteries from App\Repositories\Eloquent\Criteria namespace
     * @param array|null criteries for filter points
     * 
     * @return Illuminate\Database\Query\Builder|null
     */
    public function getPointsByCriteries(array $criteries = null) {
        if(!empty($criteries)) {
            $result = clone $this->eloquentQuery->withCriteria($criteries);
            $this->eloquentQuery->clearEntity();
            return $result;
        }
        return null;
    }
}
