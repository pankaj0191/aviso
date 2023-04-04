<?php

namespace App\Http\Controllers\Settings\Teams;

use App\Team;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\Teams\UpdateTeamPhotoRequest;
use App\Contracts\Interactions\Settings\Teams\UpdateTeamPhoto;

class TeamPhotoController extends Controller
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
     * Update the given team's photo.
     *
     * @param  \Laravel\Spark\Http\Requests\Settings\Teams\UpdateTeamPhotoRequest  $request
     * @param  \Laravel\Spark\Team  $team
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTeamPhotoRequest $request, Team $team)
    {
        $this->interaction($request, UpdateTeamPhoto::class, [$team, $request->all()]);

        return $team;
    }
}
