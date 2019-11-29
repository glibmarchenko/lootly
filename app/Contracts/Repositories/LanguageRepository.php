<?php

namespace App\Contracts\Repositories;

interface LanguageRepository
{
    public function get();

    public function find($id);
}
