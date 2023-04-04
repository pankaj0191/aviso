<?php

namespace App\Contracts;


use App\Invitation;

interface ITeamService
{
    /**
     * @param $data
     * @return mixed
     */
    public function inviteMembers($data);

    /**
     * @param $invitation
     */
    public function emailInvitation($invitation);

    /**
     * @param $team
     * @param $email
     * @param $invitedUser
     * @return mixed
     */
    public function createInvitation($team, $email, $invitedUser);

    /**
     * @param Invitation $invitation
     * @return string
     */
    public function view(Invitation $invitation);

    /**
     * @param Invitation $id
     * @return string
     */
    public function invitations($id);

    /**
     * @param $team
     * @param $member
     * @return mixed
     */
    public function deleteMember($team, $member);
}