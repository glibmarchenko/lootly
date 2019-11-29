<?php

namespace App\Traits;

use Illuminate\Database\Query\Builder;

trait QueryBuilderTrait {

   /**
   * @param Illuminate\Database\Query\Builder
   * @param array array of filters that will be applied to $bilder
   *  this array have several requirements to it structure:
   *     1. key of the element is name of function and values are it's parameters.
   *     2. functions are called in the same order as in the array
   *     3. to call function without parameters, set value as NULL (to call function with param NULL, set it in array)
   * 
   * @return mixed return builder with applied filters of value $builder if it doesn't have query
   */

   protected function applyFilters($builder, array $filters){
      if(!method_exists($builder, 'getQuery')){
         return $builder;
      }
      foreach ($filters as $function => $params) {
         if($params === null){
            $builder = $builder->$function();
         }
         elseif(is_array($params)){
            $builder = $builder->$function(...$params);
         } else {
            $builder = $builder->$function($params);
         }
      }
      return $builder;
   }
}