<?php

namespace App\Contracts\Repositories;

interface WidgetSettingsRepository
{

    /**
     * @param  mixed $billable
     * @return mixed
     */
    public function first($merchantObj);

    /**
     * @param array $data
     * @return mixed
     */
    public function createOrUpdateTabSettings($merchantObj, array $data);

    /**
     * @param array $data
     * @return mixed
     */
    public function createOrUpdateWidgetSettings($merchantObj, array $data);

    /**
     * @param array $data
     * @return mixed
     */
    public function createOrUpdateWidgetLoggedSettings($merchantObj, array $data);

    /**
     * @param array $data
     * @return mixed
     */
    public function createOrUpdateBrandingSettings($merchantObj, array $data);
}
