<?php

namespace App\Traits;

trait CurrencyFormatTrait
{

    /**
     * Properly format currency sign depends on settings
     * @param string $sign currency sign
     * @param string $value with which currency sign will be concatenated
     * @param bool $displaySign on 'true' sign will be placed before value, on false - after
     * @return string
     */
    public function formatCurrencySign(string $sign, string $value, bool $displaySign)
    {
        return $displaySign ? $sign . '' . $value : $value . ' ' . $sign;
    }
}
