<?php

namespace App\Repositories\Eloquent;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\RepositoryAbstract;
use App\Repositories\Traits\EloquentTransactional;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EloquentCustomerRepository extends RepositoryAbstract implements CustomerRepository
{
    use EloquentTransactional;

    public function entity()
    {
        return Customer::class;
    }

    public function incrementOrdersCounter($id)
    {
        $customer = $this->find($id);

        return $this->update($id, [
            'orders_count' => $customer->orders_count + 1,
        ]);
    }

    public function findReferrer($customerId)
    {
        $customer = $this->find($customerId);

        $referrer = $customer->referrer()->first();

        if (! $referrer) {
            throw (new ModelNotFoundException())->setModel(get_class($this->entity->referrer()->getModel()));
        }

        return $referrer;
    }

    public function findReferred($customerId)
    {
        $customer = $this->find($customerId);

        return $customer->referred()->get();
    }

    public function updateOrCreate(array $conditions, array $data)
    {
        $customer = $this->entity->updateOrCreate($conditions, $data);

        if ($customer->wasRecentlyCreated) {
            $customer->referral_slug = uniqid("loot");
            $customer->save();
        }

        return $customer;
    }

    public function incrementSharesCounter($id)
    {
        $customer = $this->find($id);

        return $this->update($id, [
            'shares_count' => $customer->shares_count + 1,
        ]);
    }

    public function incrementClicksCounter($id)
    {
        $customer = $this->find($id);

        return $this->update($id, [
            'clicks_count' => $customer->clicks_count + 1,
        ]);
    }

    public function countValidOrders($customerId)
    {
        $customer = $this->find($customerId);

        return $customer->orders()->where(function ($q) {
            $q->where(function ($q_) {
                $q_->where('status', '!=', 'refunded');
                $q_->where('status', '!=', 'voided');
            });
            $q->orWhereNull('status');
        })->count();
    }

    public function getTotalSpent($customerId)
    {
        $customer = $this->find($customerId);

        $paidOrders = $customer->orders()->where(function ($q) {
            $q->where(function ($q_) {
                //$q_->where('status', '!=', 'refunded');
                $q_->where('status', '!=', 'voided');
            });
            $q->orWhereNull('status');
        });

        $total_price_sum = $paidOrders->sum('total_price');
        $refunded_total_sum = $paidOrders->sum('refunded_total');

        return floatval($total_price_sum) - floatval($refunded_total_sum);
    }
}