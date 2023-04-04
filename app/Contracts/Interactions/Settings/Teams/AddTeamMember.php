<?php

namespace App\Contracts\Interactions\Settings\Teams;

interface AddTeamMember
{
    /**
     * Add a user to the given team.
     *
     * @param  \App\Team  $team
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string|null  $role
     * @return \App\Team
     */
    public function handle($team, $user, $role = null);
}
