<?php

namespace App\Interactions\Settings\Teams;

use App\Spark;
use App\Events\Teams\TeamMemberAdded;
use App\Contracts\Interactions\Settings\Teams\AddTeamMember as Contract;

class AddTeamMember implements Contract
{
    /**
     * {@inheritdoc}
     */
    public function handle($team, $user, $role = null)
    {
        $team->users()->attach($user, ['role' => $role ?: Spark::defaultRole()]);

        event(new TeamMemberAdded($team, $user));
    }
}
