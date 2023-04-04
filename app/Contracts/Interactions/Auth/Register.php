<?php

namespace App\Contracts\Interactions\Auth;

use App\Contracts\Http\Requests\Auth\RegisterRequest;
use Illuminate\Contracts\Auth\Authenticatable;

interface Register
{
    /**
     * Register a new user with the application.
     *
     * @param RegisterRequest $request
     * @return Authenticatable
     */
    public function handle(RegisterRequest $request);
}
