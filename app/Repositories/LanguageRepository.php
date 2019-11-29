<?php

namespace App\Repositories;


use App\Models\Language;
use App\Contracts\Repositories\LanguageRepository as LanguageRepositoryContract;


class LanguageRepository implements LanguageRepositoryContract
{
    public function get()
    {
        return Language::all();
    }

    public function find($id)
    {
        return Language::where('id', $id)->first();
    }

}
