<?php

namespace App\Repositories\Traits;

trait EloquentTransactional
{
    public function beginTransaction()
    {
        $db = $this->resolveDatabase();
        $db::beginTransaction();
    }
    public function rollback()
    {
        $db = $this->resolveDatabase();
        $db::rollback();
    }
    public function commit()
    {
        $db = $this->resolveDatabase();
        $db::commit();
    }
    /**
     * @param Closure $closure
     * @return mixed
     */
    public function transaction(\Closure $closure)
    {
        $db = $this->resolveDatabase();
        return $db::transaction($closure);
    }

    protected function resolveDatabase()
    {
        if (class_exists('\DB'))
            return \DB::class;
        return \Illuminate\Database\Capsule\Manager::class;
    }
}