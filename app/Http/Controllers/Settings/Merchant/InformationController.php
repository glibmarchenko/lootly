<?php

namespace App\Http\Controllers\Settings\Merchant;

use App\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\Merchant\StoreRequest;
use App\Repositories\MerchantRepository;
use App\Repositories\UserMerchantRepository;
use App\Services\Amazon\UploadFile;

class InformationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(MerchantRepository $merchantRepository, UserMerchantRepository $userMerchantRepository)
    {
        $this->merchantRepository = $merchantRepository;
        $this->userMerchantRepository = $userMerchantRepository;
        $this->middleware('auth');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function store(StoreRequest $request)
    {
        $uploadImageS3 = new UploadFile();

        $merchantObj = $request;
        $userObj = User::getAuthClient();
        $file = $merchantObj->input('logo');

        $merchantObj = $this->merchantRepository->create($userObj, $merchantObj);

        if($file) {
            $url = $uploadImageS3->upload($merchantObj, $file, $action = null);

            $this->merchantRepository->updatePhotoUrl($url, $merchantObj);
        }
        $this->userMerchantRepository->create($userObj, $merchantObj->id);
        return response()->json([
            'merchant' => $merchantObj
        ]);
    }
}
