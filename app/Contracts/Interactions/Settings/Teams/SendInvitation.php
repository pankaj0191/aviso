<?php

namespace App\Contracts\Interactions\Settings\Teams;

interface SendInvitation
{
    /**
     * Create and mail an invitation to the given e-mail address.
     *
     * @param  \App\Team  $team
     * @param  string  $email
     * @param  string  $role
     * @return \App\Invitation
     */
    public function handle($team, $email, $role);
}
