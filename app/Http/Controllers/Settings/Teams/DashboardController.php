<?php

namespace App\Http\Controllers\Settings\Teams;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
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
     * Show the team settings dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Spark\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $team)
    {
        abort_unless($request->user()->onTeam($team), 404);

        return view('spark::settings.teams.team-settings', compact('team'));
    }
}
