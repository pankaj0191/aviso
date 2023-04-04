<?php

namespace App\Contracts\Interactions\Settings\Teams;

interface UpdateTeamPhoto
{
    /**
     * Get a validator instance for the given data.
     *
     * @param  \App\Team  $team
     * @param  array  $data
     * @return \Illuminate\Validation\Validator
     */
    public function validator($team, array $data);

    /**
     * Update the team's photo.
     *
     * @param  \App\Team  $team
     * @param  array  $data
     * @return \App\Team
     */
    public function handle($team, array $data);
}
