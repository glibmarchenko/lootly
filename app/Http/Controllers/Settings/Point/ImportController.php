<?php

namespace App\Http\Controllers\Settings\Point;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\CustomerRepository;
use App\Repositories\MerchantRepository;
use App\Repositories\PointRepository;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        CustomerRepository $customerRepository,
        MerchantRepository $merchantRepository,
        PointRepository $pointRepository
    ) {
        $this->merchantRepository = $merchantRepository;
        $this->pointRepository = $pointRepository;
        $this->customerRepository = $customerRepository;

        $this->middleware('auth');
    }

    /**
     * @param Request $request
     */
    public function import(Request $request)
    {

        $request->validate([
            'title'      => 'string|nullable|max:191',
            'reason'     => 'string|nullable|max:191',
            'pointsFile' => 'base64size:5120',
        ]);

        $temp = tmpfile();
        $path = stream_get_meta_data($temp)['uri'];
        fwrite($temp, base64_decode(substr($request->pointsFile, strpos($request->pointsFile, ',') + 1)));

        try {
            Excel::load($path, function ($reader) use ($request) {
                $this->pointRepository->adjust($request, $reader->toArray());
            });
        }catch(\Exception $exception){
            fclose($temp);
            return response()->json([
                'errors' => [
                    'pointsFile' => 'Invalid file content: '.$exception->getMessage()
                ]
            ], 422);
        }

        fclose($temp);
    }

    public function downloadTemplate($file_name = 'customer-points.csv')
    {
        $file_path = storage_path($file_name);
        $headers = array(
            'Content-Type: application/csv',
        );

        return \Response::download($file_path, $file_name, $headers);
    }
}