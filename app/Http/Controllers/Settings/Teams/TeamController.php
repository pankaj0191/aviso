<?php

namespace  App\Http\Controllers\Settings\Teams;

use App\Team;
use App\Spark;
use Illuminate\Http\Request;
use App\Events\Teams\TeamDeleted;
use App\Events\Teams\DeletingTeam;
use App\Http\Controllers\Controller;
use App\Contracts\Interactions\Settings\Teams\CreateTeam;

class TeamController extends Controller
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
     * Create a new team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Spark::createsAdditionalTeams()) {
            abort(404);
        }

        $this->interaction($request, CreateTeam::class, [
            $request->user(), $request->all()
        ]);
    }

    /**
     * Delete the given team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Spark\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Team $team)
    {
        if (!$request->user()->ownsTeam($team)) {
            abort(404);
        }

        event(new DeletingTeam($team));

        $team->detachUsersAndDestroy();

        event(new TeamDeleted($team));
    }
}
