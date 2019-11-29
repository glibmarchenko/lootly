<?php

namespace App\Http\Controllers;

use App\Models\Integration;
use Illuminate\Http\Request;
use App\Repositories\MerchantRepository;
use App\Repositories\IntegrationRepository;
use App\Repositories\Eloquent\EloquentIntegrationRepository;

class IntegrationsController extends Controller
{

    public function __construct()
    {
        $this->merchantRepository = new MerchantRepository();
        $this->integrationRepository = new IntegrationRepository();
        $this->eloquentIntegrationRepository = new EloquentIntegrationRepository();
    }

    public function index(){
        return redirect(route('integrations.overview'));
    }

    public function overview()
    {
        $merchant = $this->merchantRepository->getCurrent();

        // check if merchant has permission
        if (! $merchant->checkPermitionByTypeCode(\Config::get('permissions.typecode.Integrations'))) {
            return redirect(route('integrations.upgrade'));
        }

        $integrations = Integration::orderBy('order')->with('merchant')->get();
        $plan = $merchant->plan();

        $api = $plan->growth_order > 1 ? [
            'View Documentation',
            'href=https://documenter.getpostman.com/view/4144738/SVmtxeUY?version=latest#e03aee4f-f0cb-445f-b918-8392f1bef153 target="_block"'
        ] : [
            'Upgrade to Ultimate',
            'href=/account/upgrade'
        ];

        $integrationsLinks = [
            'shopify' => [
                'connected' => ['Connected', ''],
                'not-connected' => ['Connect', 'href=/integrations/manage/edit/shopify'],
            ],
            'woocommerce' => [
                'connected' => ['Connected', ''],
                'not-connected' => ['Learn More', ' href=/apps/woocommerce'],
            ],
            'volusion' => [
                'connected' => ['Connected', ''],
                'not-connected' => ['Learn More', ' href=/apps/volusion'],
            ],
            'trustspot' => [
                'connected' => ['Connected', ''],
                'not-connected' => ['Learn More', 'v-b-modal.trustspot-modal'],
            ],
            'api' => [
                'connected' => $api,
                'not-connected' => $api,
            ],
        ];

        return view('integrations.overview', compact('integrations', 'merchant', 'integrationsLinks', 'plan'));
    }

    public function edit($slug)
    {
        $merchantModel = $this->merchantRepository->getCurrent();

        try {
            $integration = $this->integrationRepository->findBySlug($slug);

            $id = $integration->id;
            $plan = $merchantModel->plan();

            if (! $integration->showForPlan($plan)) {
                abort(404);
            }

            try {
                return view("integrations.manage.edit.$slug", compact('id'));

            } catch (\InvalidArgumentException $e) {
                abort(404);
            }

        } catch (\ErrorException $e) {
            abort(404);
        }
    }

    public function storeSuggestion(Request $request){
        $request->validate([
            'companyName' => 'required|max:10',
            'website' => 'required|max:190',
            'body' => 'max:65535',
        ]);
        $merchant = $this->merchantRepository->getCurrent();
        $data = $request->all();
        $data['merchantId']  =  $merchant->id;
        $data['merchantCompanyName'] = $merchant->name;
        if(!$this->integrationRepository->storeSuggestedIntegration($data)){
            return response()->json(['message' => 'Error in saving suggestion'], 500);
        }
        return response()->json(['message' => 'Your suggestion has been submitted successfully', 200]);
    }
}