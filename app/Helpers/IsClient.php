<?php

/**
 * Check if user has projects as client
 * @param $user
 * @return bool
 */
function isClient($user)
{
    // $clientProjects = $user->projects()->where('role', 'client')->count();

    if (currentRole($user) == 'Client') {
        return true;
    }

    return false;
}