<?php

namespace App\Contracts\Repositories\Geography;

interface CountryRepository
{
    /**
     * Get all of the countries in the world.
     *
     * @return array
     */
    public function all();
}
