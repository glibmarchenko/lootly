<?php

namespace App\Repositories;


use App\Models\Integration;
use App\Models\Tag;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Contracts\Repositories\MerchantRepository as MerchantContractRepository;
use App\Merchant;
use App\Services\Amazon\UploadFile;
use Laravel\Spark\Repositories\TeamRepository;

class MerchantRepository extends TeamRepository
{



    private $baseQuery;

    public function __construct()
    {
        $this->baseQuery = Merchant::query();

    }

    /**
     * @param $id
     * @param $with
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function find($id, $with = [])
    {
        return Merchant::where('id', $id)->with($with)->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function get()
    {
        return \Spark::team()->get();
    }

    public function create($user, array $data)
    {
        $attributes = [
            'owner_id' => $user->id,
            'name' => $data['name'],
            'slug' => uniqid('store_', true),
            /*'website' => $data['website'],
            'billing_country' => $data['selectedCountry'],*/
            'trial_ends_at' => Carbon::now()->addDays(\Spark::teamTrialDays()),
        ];

        if (\Spark::teamsIdentifiedByPath()) {
            $attributes['slug'] = $data['slug'];
        }

        //$this->putSessionId($merchantObj->id);

        return \Spark::team()->forceCreate($attributes);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function putSessionId($id)
    {
        return $current_id = \Session::put('current_store', $id);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getSessionId($key)
    {
        return $current_id = Session::get($key);
    }

    public function update($merchant, array $data)
    {
        return $merchant->forceFill([
                'name' => $data['name'],
                'currency_id' => $data['currency_id'],
                'currency_display_sign' => isset($data['currency_display_sign']) && $data['currency_display_sign'] ? 1 : 0,
                'language' => $data['language'],
                //'customer_earned_point_notification' => isset($merchant['customer_earned_point_notification']) && $merchant['customer_earned_point_notification'] ? 1 : 0,
                //'customer_spent_point_notification' => isset($merchant['customer_spent_point_notification']) && $merchant['customer_spent_point_notification'] ? 1 : 0,
            ])->save();
    }

    public function updatePhotoUrl($url, $merchantObj)
    {
        $this->baseQuery->where('id', '=', $merchantObj->id)
            ->update([
                'logo_url' => $url
            ]);
    }

    /**
     * @param $user
     */
    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function getCurrent()
    {   
        $id = Auth::id();
        return CacheRepository::rememberCacheByTag( User::class . $id , \get_class($this) . '@find',        function() use ($id){
            return User::find($id)->current_team;
        });

        /*$current_id = $this->getSessionId('current_store');

        if (!$current_id) {

            return $this->baseQuery
                ->select('users.*', 'merchants.*', 'merchants.id as id')
                ->join('users', 'users.id', '=', 'merchants.owner_id')
                ->where('users.id', '=', Auth::user()->id)
                ->first();
        } else {

            return $this->baseQuery
                ->select('users.*', 'merchants.*', 'merchants.id as id')
                ->join('users', 'users.id', '=', 'merchants.owner_id')
                ->where('merchants.owner_id', '=', Auth::user()->id)
                ->where('merchants.id', '=', $current_id)
                ->first();

        }*/

    }

    public function current()
    {
        $id = Auth::id();
        return CacheRepository::rememberCacheByTag( User::class . $id , \get_class($this) . '@find', function() use ($id){
            return User::find($id)->current_team;
        });

        /*$current_id = $this->getSessionId('current_store');

        if (!$current_id) {
            return $this->baseQuery
                ->where('merchants.owner_id', '=', Auth::user()->id)
                ->first();
        } else {
            return $this->baseQuery
                ->where('merchants.owner_id', '=', Auth::user()->id)
                ->where('merchants.id', '=', $current_id)
                ->first();
        }*/

    }

    public function getTags($merchantObj)
    {
        if($merchantObj) {
            return $merchantObj->tags()->get();
        }else{
            return collect([]);
        }
    }

    public function createTags($merchantObj, $tags = [])
    {
        $newTags = [];

        foreach($tags as $tag) {
            $newTags[] = new Tag(['name' => $tag]);
        }

        $new = $merchantObj->tags()->saveMany($newTags);

        return $new;
    }

    public function getMerchantsByOwner($id)
    {
        return $this->baseQuery->where('owner_id', '=', $id)->get();
    }

    public function findOwnedMerchantById($ownerId, $merchantId)
    {
        return $this->baseQuery
            ->where('id', '=', $merchantId)
            ->where('owner_id', '=', $ownerId)
            ->first();
    }

    public function getIntegrations(Merchant $merchant)
    {
        return $merchant->integrations;
    }

    public function findIntegration(Merchant $merchant, Integration $integration)
    {
        return $merchant->integrations()->where('integration_id', $integration->id)->first();
    }

    public function updateIntegration(Merchant $merchant, Integration $integration, array $data = [])
    {
        return $merchant->integrations()->syncWithoutDetaching([$integration->id => $data]);
    }

    public function findIntegrationWithToken(Merchant $merchant, Integration $integration)
    {
        return $merchant->integrationsWithToken()->where('integration_id', $integration->id)->first();
    }

    public function updateIntegrations($merchant, $integration_id, array $data)
    {
        $merchant->integrationsWithToken()->syncWithoutDetaching([$integration_id => $data]);
    }

    public function getDetails($merchant)
    {
        $details = $merchant->detail;
        if($details){
            return $details->makeVisible(['api_key', 'api_secret']);
        }
        return null;
    }

    public function saveDetails($merchant, array $data)
    {
        return $merchant->detail()->updateOrCreate([
            'merchant_id' => $merchant->id
        ], $data);
    }

}
