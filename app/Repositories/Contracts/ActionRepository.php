<?php

namespace App\Repositories\Contracts;

interface ActionRepository
{
    public function findBySlug($slug);
}