<?php

/**
 * Check if user has projects as freelancer
 * @param $user
 * @return bool
 */
function IsAgency($user)
{
    if (currentRole($user) == 'Agency') {
        return true;
    }

    return false;
}