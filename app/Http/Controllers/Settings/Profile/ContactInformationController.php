<?php

namespace App\Http\Controllers\Settings\Profile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Contracts\Interactions\Settings\Profile\UpdateContactInformation;

class ContactInformationController extends Controller
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
     * Update the user's contact information settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->interaction(
            $request, UpdateContactInformation::class,
            [$request->user(), $request->all()]
        );
    }
}
