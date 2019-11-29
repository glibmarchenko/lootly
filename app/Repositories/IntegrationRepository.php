<?php

namespace App\Repositories;


use App\Contracts\Repositories\IntegrationRepository as IntegrationRepositoryContract;
use App\Models\Integration;
use App\Models\SuggestedIntegrationModel;
use App\Merchant;

class IntegrationRepository implements IntegrationRepositoryContract
{
    public function all()
    {
        return Integration::all();
    }

    public function get()
    {
        return Integration::where('status', 1)->get();
    }

    public function getSlugList()
    {
        return Integration::where('status', 1)->get()->pluck('id', 'slug')->toArray();
    }

    public function find($id)
    {
        return Integration::where('id', $id)->first();
    }

    public function findBySlug($slug)
    {
        return Integration::where('slug', $slug)->first();
    }

    public function findActiveBySlug($slug)
    {
        return Integration::where(['slug' => $slug, 'status' => 1])->first();
    }

    public function getMerchantsWithActiveIntegration(Integration $integration, $externalId = null)
    {
        if(!$externalId){
            return $integration->merchant()->where([
                'merchant_integrations.status' => 1
            ])->get();
        }

        return $integration->merchant()->where([
            'merchant_integrations.status' => 1,
            'merchant_integrations.external_id' => $externalId,
        ])->get();
    }

    /**
     * Create new Suggestion
     * @param array['merchantId', 'merchantCompanyName', 'companyName', 'website', 'body']
     * @return SuggestedIntegrationModel|false
     */

    public function storeSuggestedIntegration($data){
        $newSuggestedIntegration = new SuggestedIntegrationModel;
        $newSuggestedIntegration->merchant_id = $data['merchantId'];
        $newSuggestedIntegration->merchant_company_name = $data['merchantCompanyName'];
        $newSuggestedIntegration->integration_company_name = $data['companyName'];
        $newSuggestedIntegration->website = $data['website'];
        $newSuggestedIntegration->question = $data['body'];

        try{
            return $newSuggestedIntegration->save();
        } catch(\Exeption $e) {
            Debugbar::error($e);
            return false;
        }
    }
}
