<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileDetailsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Update the user's profile details.
     *
     * @return Response
     */
    public function update(Request $request)
    {
        $request->user()->forceFill([
            'city' => $request->city,
            'address' => $request->address,
            'country_code' => $request->country_code,
            'phone' => $request->phone,
            'company' => $request->company,
            'freelancer_name' => $request->freelancer_name,
        ])->save();
    }
}
