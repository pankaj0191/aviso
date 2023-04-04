<?php

namespace App\Http\Middleware;

use App\Spark;

class VerifyUserHasTeam
{
    /**
     * Verify the incoming request's user belongs to team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        if (Spark::usesTeams() && $request->user() && ! $request->user()->hasTeams()) {
            return redirect(Spark::teamsPrefix().'/missing');
        }

        return $next($request);
    }
}
