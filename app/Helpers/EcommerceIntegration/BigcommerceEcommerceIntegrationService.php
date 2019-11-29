<?php

namespace App\Helpers\EcommerceIntegration;

use App\Repositories\Contracts\IntegrationRepository;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use Illuminate\Support\Facades\Log;
use App\Repositories\Contracts\PointRepository;

class BigcommerceEcommerceIntegrationService extends BaseEcommerceIntegrationService
{
    protected $integrations;

    protected $points;

    public function __construct( IntegrationRepository $integrations, PointRepository $points )
    {
        parent::__construct( $points );
        $this->integrations = $integrations;
    }

    public function getBigcommerceIntegration() {
        try {
            $bigcommerceIntegration = $this->integrations->findWhereFirst([
                'slug'   => 'bigcommerce',
                'status' => 1,
            ]);
        } catch (\Exception $e) {
            // No BigCommerce integration
        }

        // Check Bigcommerce integration status
        if (! isset( $bigcommerceIntegration) || ! $bigcommerceIntegration ) {
            return abort(500, 'Bigcommerce integraion is disabled.');
        }
        return $bigcommerceIntegration;
    }

    public function getMerchant( $shopDomain ) {

        $targetMerchants = $this->getMerchants( $shopDomain );
        //dd( $targetMerchants );
        if (count( $targetMerchants ) > 1) {
            return redirect()->route('login');
        }
        elseif (count( $targetMerchants ) == 1 ) {
            return $targetMerchants[0];
        }
    }

    public function getMerchants( $shopDomain ) {

        $bigcommerceIntegration = $this->getBigcommerceIntegration();
        $this->integrations->clearEntity();
        $targetMerchants = $this->integrations->withCriteria([
            new LatestFirst(),
        ])->findMerchantWhere($bigcommerceIntegration->id, [
            'status'      => 1,
            'external_id' => $shopDomain,
        ]);

        return $targetMerchants;
    }

    public function makeScriptApiRequests( $endpoint, $access_token, $hash, $merchant_details )
    {

        //Don't delete
        Log::info( 'BC shop was connected. Parameters for API request are below');
        Log::info( 'Access token: ' . $access_token );
        Log::info( 'Hash: ' . $hash );

        $script = "<script>
                //Make signature
                let customer_id = '{{customer.id}}';
                let api_secret = '" . $merchant_details->api_secret . "';                
                var MD5 = function(d){result = M(V(Y(X(d),8*d.length)));return result.toLowerCase()};function M(d){for(var _,m=\"0123456789ABCDEF\",f=\"\",r=0;r<d.length;r++)_=d.charCodeAt(r),f+=m.charAt(_>>>4&15)+m.charAt(15&_);return f}function X(d){for(var _=Array(d.length>>2),m=0;m<_.length;m++)_[m]=0;for(m=0;m<8*d.length;m+=8)_[m>>5]|=(255&d.charCodeAt(m/8))<<m%32;return _}function V(d){for(var _=\"\",m=0;m<32*d.length;m+=8)_+=String.fromCharCode(d[m>>5]>>>m%32&255);return _}function Y(d,_){d[_>>5]|=128<<_%32,d[14+(_+64>>>9<<4)]=_;for(var m=1732584193,f=-271733879,r=-1732584194,i=271733878,n=0;n<d.length;n+=16){var h=m,t=f,g=r,e=i;f=md5_ii(f=md5_ii(f=md5_ii(f=md5_ii(f=md5_hh(f=md5_hh(f=md5_hh(f=md5_hh(f=md5_gg(f=md5_gg(f=md5_gg(f=md5_gg(f=md5_ff(f=md5_ff(f=md5_ff(f=md5_ff(f,r=md5_ff(r,i=md5_ff(i,m=md5_ff(m,f,r,i,d[n+0],7,-680876936),f,r,d[n+1],12,-389564586),m,f,d[n+2],17,606105819),i,m,d[n+3],22,-1044525330),r=md5_ff(r,i=md5_ff(i,m=md5_ff(m,f,r,i,d[n+4],7,-176418897),f,r,d[n+5],12,1200080426),m,f,d[n+6],17,-1473231341),i,m,d[n+7],22,-45705983),r=md5_ff(r,i=md5_ff(i,m=md5_ff(m,f,r,i,d[n+8],7,1770035416),f,r,d[n+9],12,-1958414417),m,f,d[n+10],17,-42063),i,m,d[n+11],22,-1990404162),r=md5_ff(r,i=md5_ff(i,m=md5_ff(m,f,r,i,d[n+12],7,1804603682),f,r,d[n+13],12,-40341101),m,f,d[n+14],17,-1502002290),i,m,d[n+15],22,1236535329),r=md5_gg(r,i=md5_gg(i,m=md5_gg(m,f,r,i,d[n+1],5,-165796510),f,r,d[n+6],9,-1069501632),m,f,d[n+11],14,643717713),i,m,d[n+0],20,-373897302),r=md5_gg(r,i=md5_gg(i,m=md5_gg(m,f,r,i,d[n+5],5,-701558691),f,r,d[n+10],9,38016083),m,f,d[n+15],14,-660478335),i,m,d[n+4],20,-405537848),r=md5_gg(r,i=md5_gg(i,m=md5_gg(m,f,r,i,d[n+9],5,568446438),f,r,d[n+14],9,-1019803690),m,f,d[n+3],14,-187363961),i,m,d[n+8],20,1163531501),r=md5_gg(r,i=md5_gg(i,m=md5_gg(m,f,r,i,d[n+13],5,-1444681467),f,r,d[n+2],9,-51403784),m,f,d[n+7],14,1735328473),i,m,d[n+12],20,-1926607734),r=md5_hh(r,i=md5_hh(i,m=md5_hh(m,f,r,i,d[n+5],4,-378558),f,r,d[n+8],11,-2022574463),m,f,d[n+11],16,1839030562),i,m,d[n+14],23,-35309556),r=md5_hh(r,i=md5_hh(i,m=md5_hh(m,f,r,i,d[n+1],4,-1530992060),f,r,d[n+4],11,1272893353),m,f,d[n+7],16,-155497632),i,m,d[n+10],23,-1094730640),r=md5_hh(r,i=md5_hh(i,m=md5_hh(m,f,r,i,d[n+13],4,681279174),f,r,d[n+0],11,-358537222),m,f,d[n+3],16,-722521979),i,m,d[n+6],23,76029189),r=md5_hh(r,i=md5_hh(i,m=md5_hh(m,f,r,i,d[n+9],4,-640364487),f,r,d[n+12],11,-421815835),m,f,d[n+15],16,530742520),i,m,d[n+2],23,-995338651),r=md5_ii(r,i=md5_ii(i,m=md5_ii(m,f,r,i,d[n+0],6,-198630844),f,r,d[n+7],10,1126891415),m,f,d[n+14],15,-1416354905),i,m,d[n+5],21,-57434055),r=md5_ii(r,i=md5_ii(i,m=md5_ii(m,f,r,i,d[n+12],6,1700485571),f,r,d[n+3],10,-1894986606),m,f,d[n+10],15,-1051523),i,m,d[n+1],21,-2054922799),r=md5_ii(r,i=md5_ii(i,m=md5_ii(m,f,r,i,d[n+8],6,1873313359),f,r,d[n+15],10,-30611744),m,f,d[n+6],15,-1560198380),i,m,d[n+13],21,1309151649),r=md5_ii(r,i=md5_ii(i,m=md5_ii(m,f,r,i,d[n+4],6,-145523070),f,r,d[n+11],10,-1120210379),m,f,d[n+2],15,718787259),i,m,d[n+9],21,-343485551),m=safe_add(m,h),f=safe_add(f,t),r=safe_add(r,g),i=safe_add(i,e)}return Array(m,f,r,i)}function md5_cmn(d,_,m,f,r,i){return safe_add(bit_rol(safe_add(safe_add(_,d),safe_add(f,i)),r),m)}function md5_ff(d,_,m,f,r,i,n){return md5_cmn(_&m|~_&f,d,_,r,i,n)}function md5_gg(d,_,m,f,r,i,n){return md5_cmn(_&f|m&~f,d,_,r,i,n)}function md5_hh(d,_,m,f,r,i,n){return md5_cmn(_^m^f,d,_,r,i,n)}function md5_ii(d,_,m,f,r,i,n){return md5_cmn(m^(_|~f),d,_,r,i,n)}function safe_add(d,_){var m=(65535&d)+(65535&_);return(d>>16)+(_>>16)+(m>>16)<<16|65535&m}function bit_rol(d,_){return d<<_|d>>>32-_}
                let signature = MD5( customer_id + api_secret );
                                
                //Add scripts
                let wrapper = document.createElement('DIV');
                wrapper.setAttribute('id','lootly-widget');
                wrapper.setAttribute('class','lootly-init');
                wrapper.setAttribute('data-provider', '" . env('APP_URL') . "');
                wrapper.setAttribute('data-api-key', '" . $merchant_details->api_key . "');
                wrapper.setAttribute('data-shop-domain','" . $merchant_details->ecommerce_shop_domain . "');
                wrapper.setAttribute('data-shop-id','" . md5($merchant_details->ecommerce_shop_domain . $merchant_details->api_secret ) . "');
                wrapper.setAttribute('data-customer-id', '{{customer.id}}' );
                wrapper.setAttribute('data-customer-signature', signature );
                let footer = document.getElementsByTagName('footer')[0];
                footer.append(wrapper);
                </script>";

        $post = json_encode( [
            "name" => "Lootly widget wrapper",
            "description" => "HTML wrapper for Lootly widget",
            "html" => $script,
            "auto_uninstall" => true,
            "load_method" => "default",
            "location" => "footer",
            "visibility" => "all_pages",
            "kind" => "script_tag"
        ] );

        $this->makeApiCall( $endpoint, $post, $hash, $access_token );


        $post = json_encode( [
            "name" => "Lootly widget script",
            "description" => "Script for Lootly widget",
            "src" => env('APP_URL') . "/js/integrations/bigcommerce/script.js",
            "auto_uninstall" => true,
            "load_method" => "default",
            "location" => "footer",
            "visibility" => "all_pages",
            "kind" => "src"
        ] );

        $this->makeApiCall( $endpoint, $post, $hash, $access_token );
    }

    public function removeLootlyScripts( $access_token, $hash, $merchant_details ) {

        //Getting all scripts
        $uuids_to_delete = [];
        $all_scripts = $this->makeApiCall( 'v3/content/scripts', null, $hash, $access_token );

        //Getting Lootly scripts
        foreach( $all_scripts->data as $script ){
            if( $script->name == 'Lootly widget wrapper' || $script->name == 'Lootly widget script' ) {
                $uuids_to_delete[] = $script->uuid;
            }
        }

        foreach ( $uuids_to_delete as $uuid_to_delete ) {
            $this->makeApiCall( 'v3/content/scripts/' . $uuid_to_delete, 'delete', $hash, $access_token );
        }
    }

    public function sendGetProductsRequest($requestParams)
    {
        try {

            //Getting hash & token
            $hash = $this->eCommerceIntegration->pivot->external_id;
            $token = $this->eCommerceIntegration->pivot->token;

            $productsResponse = $this->makeApiCall( 'v3/catalog/products?keyword_context=name&keyword=' . $requestParams['title'], null, $hash, $token )->data;
        } catch (\Exception $exception) {
            throw $exception;
        }

        return array_map(function ($item) {
            $output = [
                'id'            => $item->id,
                'title'         => $item->name,
                'default_price' => floatval( $item->price ),
            ];

            return $output;
        }, $productsResponse);
    }

    public function sendGenerateDiscountRequest( $discountData )
    {

        try {

            //Getting hash & token
            $hash = $this->eCommerceIntegration->pivot->external_id;
            $token = $this->eCommerceIntegration->pivot->token;

            switch ( 'target type: ' . $discountData[ 'target_type' ] . '; target: ' . $discountData[ 'target_selection' ] . '; value type: ' . $discountData[ 'value_type' ] ) {

                //Free product
                case 'target type: line_item; target: entitled; value type: fixed_amount':
                    $applies_to_ids = $discountData[ 'entitled_product_ids' ];
                    $entity = 'products';
                    $type = 'per_item_discount';
                    $amount = - $discountData[ 'value' ];
                    $name = 'Free product ';
                    break;

                //Percentage off
                case 'target type: line_item; target: all; value type: percentage':
                    $applies_to_ids = [0];
                    $entity = 'categories';
                    $type = 'percentage_discount';
                    $amount = - $discountData[ 'value' ];
                    $name = 'Percentage ';
                    break;

                //Free shipping
                case 'target type: shipping_line; target: all; value type: percentage':
                    $applies_to_ids = [0];
                    $entity = 'categories';
                    $type = 'free_shipping';
                    $amount = 0;
                    $name = 'Free shipping ';
                    break;

                //Amount ( fixed or variable )
                case 'target type: line_item; target: all; value type: fixed_amount':
                    $applies_to_ids = [0];
                    $entity = 'categories';
                    $type = 'per_total_discount';
                    $amount = - $discountData[ 'value' ];
                    $name = 'Amount ';
                    break;
            }

            $min_purchase = 0;
            if( array_key_exists('prerequisite_subtotal_range', $discountData ) ) {
                $min_purchase = floatval( $discountData['prerequisite_subtotal_range']['greater_than_or_equal_to'] );
            }

            $post = json_encode(
                [
                    "name" => $name . $discountData['title'],
                    "type" => $type,
                    "code" => $discountData['title'],
                    "enabled" => true,
                    "amount" => $amount,
                    "min_purchase" => $min_purchase,
                    "max_uses" => 1,
                    "applies_to" => [
                        "entity" => $entity,
                        "ids" =>  $applies_to_ids
                    ]
                ] );

            $couponResponse = $this->makeApiCall( 'v2/coupons', $post, $hash, $token );

        } catch (\Exception $exception) {
            throw $exception;
        }

        return [
            'id'   => $couponResponse->id,
            'code' => $couponResponse->code,
        ];
    }

    public function makeApiCall( $endpoint, $post, $hash, $access_token ) {

        $ch = curl_init('https://api.bigcommerce.com/' . $hash . '/' . $endpoint );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        if( $post == 'delete' ) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        }
        elseif( !is_null( $post ) ) {
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $post );
        }
        else {
            curl_setopt($ch, CURLOPT_HEADER, 0);
        }
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
            'X-Auth-Client: ' . env('BC_APP_CLIENT_ID'),
            'X-Auth-Token: ' . $access_token,
            'Content-Type: application/json',
            'Accept: application/json'
        ) );

        $response = curl_exec( $ch );
        curl_close( $ch );

        return json_decode( $response );
    }

    public function getStoreHash(Request $request) {
        if (env('APP_ENV') === 'local') {
            return env('BC_LOCAL_STORE_HASH');
        } else {
            return $request->session()->get('store_hash');
        }
    }

    public function apiClient()
    {
        // TODO: Implement apiClient() method.
    }

    public function sendGetCustomerRequest($customerId)
    {
        $hash = $this->eCommerceIntegration->pivot->external_id;
        $token = $this->eCommerceIntegration->pivot->token;
        $output = [];
        try {
            $customerData = $this->makeApiCall( 'v2/customers/'.$customerId, null, $hash, $token );

            $output = [
                'first_name' => $customerData->first_name ?? null,
                'last_name'  => $customerData->last_name ?? null,
                'email'      => $customerData->email,
                'phone'      => $customerData->phone ?? null,
            ];

        } catch (\Exception $exception) {
            throw $exception;
        }

        return $output;
    }

    public function sendUninstallIntegrationRequest()
    {
        // TODO: Implement sendUninstallIntegrationRequest() method.
    }
}
