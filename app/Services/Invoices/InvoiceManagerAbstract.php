<?php


abstract class InvoiceManagerAbstract
{
    /**
     * @param object $obj
     * @return mixed
     */
    public abstract function createInvoice($obj);


    /**
     * @param object $obj
     * @return string
     */
    public function getName($obj){
        return $obj->name;
    }
}