<?php

namespace App\Repositories\Contracts;

interface TierHistoryRepository
{
   public function getByMerchant(int $merchantId);

   public function getByPeriod(\DatePeriod $period, \App\Merchant $merchant = null);
}