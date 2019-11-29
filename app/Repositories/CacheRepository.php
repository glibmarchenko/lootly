<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
class CacheRepository
{

    /**
     * Create cache for tag or return if exist. Tag is created from model's class name and attribute
     * @param object of model for which cache will be remembered
     * @param string key of the cache
     * @param \Closure function that return data which be stored if there on cache for the entity
     * @param int cache lifetime
     * @param string name of attribute based on which tag was used
     * 
     * @return bool
     */
    public static function rememberCacheByObject($object, string $cacheKey, \Closure $function, int $lifeTime = null, string $attribute = 'id'){
        if(!isset($object) || !is_object($object)){
            return false;
        }
        if(empty($lifeTime)){
            $lifeTime = env('CACHE_LIFETIME', 1440);
        }
        try{
            return Cache::tags(get_class($object) . $object[$attribute])->remember($cacheKey, $lifeTime, $function);
        } catch (\Exeption $e){
            \Log::error($e);
            \Log::info("Notice that 'file' and 'database' cache drivers don't support tags");
            throw $e;
        }
    }

    /**
     * Create cache for tag or return if exist. Tag should be created from model's class name and key
     * @param string Name of the tag. Must be based on model's class name and key attribute. Example: App\User1
     * @param string key of the cache
     * @param \Closure function that return data which be stored if there on cache for the entity
     * @param int cache lifetime
     * 
     * @return bool
     */
    public static function rememberCacheByTag(string $tagName, string $cacheKey, \Closure $function, int $lifeTime = null){
        if(empty($tagName)){
            return false;
        }
        if(empty($lifeTime)){
            $lifeTime = env('CACHE_LIFETIME', 1440);
        }
        try{
            return Cache::tags($tagName)->remember($cacheKey, $lifeTime, $function);
        } catch (\Exeption $e){
            \Log::error($e);
            \Log::info("Notice that 'file' and 'database' cache drivers don't support tags");
            throw $e;
        }
    }

    /**
     * Clear cache for tag based on model's class name and attribute
     * @param Model of model for which cache will be cleaned
     * @param string name of attribute based on which tag was created
     * 
     * @return bool
     */
    public static function clearCache(Model $model, string $attribute = 'id'){
        if(!isset($model) || !is_object($model)){
            return false;
        }

        try{
            Cache::tags(get_class($model) . $model[$attribute])->flush();
        } catch (\Exeption $e){
            \Log::error($e);
        }

        return true;
    }
}
