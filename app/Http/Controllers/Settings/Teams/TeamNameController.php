<?php

namespace App\Http\Controllers\Settings\Teams;

use App\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TeamNameController extends Controller
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
     * Update the given team's name.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Team $team)
    {
        abort_unless($request->user()->ownsTeam($team), 404);

        $this->validate($request, [
            'name' => 'required|max:255',
        ]);

        $team->forceFill([
            'name' => $request->name,
        ])->save();
    }
}
