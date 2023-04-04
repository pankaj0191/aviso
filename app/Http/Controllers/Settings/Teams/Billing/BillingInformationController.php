<?php

namespace App\Http\Controllers\Settings\Teams\Billing;

use App\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BillingInformationController extends Controller
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
     * Update the team's extra billing information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Spark\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Team $team)
    {
        abort_unless($request->user()->ownsTeam($team), 403);

        $this->validate($request, [
            'information' => 'max:2048',
        ]);

        $team->forceFill([
            'extra_billing_information' => $request->information,
        ])->save();
    }
}
