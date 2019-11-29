<?php

namespace App\Console\Commands;


use App\Models\Customer;
use App\Models\Point;
use Illuminate\Console\Command;
use Laravel\Spark\Repositories\PointRepository;

class AddPointsForBirthday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:points';


    public function handle()
    {
        $today = date("Y/m/d");
        $points = Customer::query()->where('birthday', '=', $today)
            ->select('points.*')
            ->Join('points', 'points.customer_id', '=', 'customers.id')
            ->get()->toArray();

        foreach ($points as $point) {
            $point['point_value'] = $point['point_value'] + 1;
            Point::query()->create($point);
        }

    }

}
