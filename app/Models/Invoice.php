<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use StripeInvoiceManager;

class Invoice extends Model
{


    protected $table = 'invoices';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'team_id', 'provider_id', 'total', 'tax', 'card_county', 'billing_state', 'billing_zip', 'billing_county', 'vat_idFF'
    ];
    public function getInvoiceManager(){
        return new StripeInvoiceManager($this);
    }
}
