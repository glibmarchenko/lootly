<?php

namespace App\Http\Controllers\Settings\Customer;

use App\Transformers\CustomerExportTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\CustomerRepository;
use App\Repositories\MerchantRepository;
use App\Repositories\PointRepository;
use Maatwebsite\Excel\Facades\Excel;

class ImportExportController extends Controller
{
    
    protected $fileRowsCount;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CustomerRepository $customerRepository, MerchantRepository $merchantRepository,
                                PointRepository $pointRepository)
    {
        $this->pointRepository = $pointRepository;
        $this->merchantRepository = $merchantRepository;
        $this->customerRepository = $customerRepository;
        $this->middleware('auth');
        ini_set('max_execution_time', 240);        
    }

    /**
     * @param Request $request
     */
    public function export(Request $request)
    {
        $merchantObj = $this->merchantRepository->getCurrent();
        $customers = $this->customerRepository->getForExport($merchantObj);
        $start = $request->get('start');
        $end = $request->get('end');
        if (isset($start) && isset($end)) {
            $startDate = new \DateTime($start);
            $endDate = new \DateTime($end);
            $customers = $customers
                ->where('created_at', '>', $startDate)
                ->where('created_at', '<', $endDate);
        }
        $search = $request->get('search');
        if (isset($search) && !empty($search)) {
            $customers = $customers->filter(function ($item) use ($search) {
                return stripos($item->name, $search) !== false || stripos($item->email, $search) !== false;
            });
        }

        $tier = $request->get('tier');
        if (!empty($tier) && $tier != 'All') {
            $customers = $customers->filter(function ($customer) use ($tier) {
                if (empty($customer->tier)) {
                    return false;
                }
                return $customer->tier->id == $tier;
            });
        }
        $transformedData = fractal()->collection($customers)->transformWith(new CustomerExportTransformer)->toArray();
        Excel::create('customers-' . date('Y-m-d--H-i-s'), function ($excel) use ($transformedData) {
            $excel->setTitle('Customer Export');
            $excel->sheet('Excel sheet', function ($sheet) use ($transformedData) {
                $sheet->setOrientation('landscape');
                $sheet->fromArray($transformedData['data']);
            });
        })->export('csv');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'importFile' => 'base64size:5120',
            'awardPoints' => 'boolean'
        ]);

        $temp = tmpfile();
        $path = stream_get_meta_data($temp)['uri'];
        fwrite($temp, base64_decode(substr($request->importFile, strpos($request->importFile, ',') + 1)));

        try {
            Excel::load($path, function ($reader) use ($request) {
                $this->fileRowsCount = $reader->getActiveSheet()->getHighestRow();
                if($this->fileRowsCount <= 1000) {
                    $merchant = $this->merchantRepository->getCurrent();
                    $this->customerRepository->add($merchant, $reader->toArray(), $request->awardPoints);
                }
            });

            if($this->fileRowsCount > 1000) {
                return response()->json([
                    'errors' => [
                        'message' => 'Cannot import '.number_format($this->fileRowsCount).' rows at once. Max 1,000 rows per file.'
                    ]
                ], 422);
            }

        } catch (\Exception $exception) {
            fclose($temp);
            return response()->json([
                'errors' => [
                    'importFile' => 'Invalid file content: ' . $exception->getMessage()
                ]
            ], 422);
        }

        fclose($temp);
    }

    /**
     * @param string $file_name
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplate($file_name = 'customer.csv')
    {
        $file_path = storage_path($file_name);
        $headers = [
            'Content-Type: application/csv',
        ];

        return response()->download($file_path, $file_name, $headers);
    }
}
