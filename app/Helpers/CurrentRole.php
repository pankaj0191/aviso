<?php

use App\Profile;

/**
 * Check user current role
 * @param $user
 * @return bool
 */
function currentRole($user)
{
        $profile = Profile::where('id', $user->current_profile_id)->first();
        return $profile->profileType->name;
}