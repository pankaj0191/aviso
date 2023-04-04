<?php

namespace App\Services;

use App\Team;
use App\User;
use App\Invitation;
use Ramsey\Uuid\Uuid;
use App\Mail\TeamInvitation;
use App\Contracts\ITeamService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Events\Prooflo\Teams\TeamMemberRemoved;
use App\Events\Prooflo\Teams\UserInvitedToTeam;

class TeamService implements ITeamService
{
    protected $model;

    public function __construct(Team $model)
    {
        $this->model = $model;
    }

    /**
     * Invite team member
     * @param $data
     * @return mixed
     */
    public function inviteMembers($data)
    {
        $team = $this->model->where('id', $data['team_id'])->first();
        $user = \Auth::user();
        $currentPlan = $user->currentPlan();
        $ownedTeam = $user->ownedTeams()->first();
        $exitingMembers = $ownedTeam ? $ownedTeam->users()->where('id', '<>', $user->id)->count() : [];
        $pendingEmail = $ownedTeam ? $ownedTeam->invitations->count() : [];
        if ($currentPlan && $user->subscribedToPlan($currentPlan->stripe_plan_id, 'default')) {
            if ($exitingMembers + $pendingEmail < $currentPlan->teams_members_count - 1) {
                $freelancer = $data['freelancer'];
                $invitations = [];

                    $invitedUser = User::where('email', $freelancer)->first();
                    $this->emailInvitation(
                        $invitation = $this->createInvitation($team, $freelancer, $invitedUser)
                    );

                    $invitations[] = $invitation;
                    if ($invitedUser) {
                        event(new UserInvitedToTeam($team, $invitedUser));
                    }

                return $invitations;
            }
        }
    }

    /**
     * Email user about team invitation
     * @param $invitation
     */
    public function emailInvitation($invitation)
    {
        $view = $this->view($invitation);
        Mail::to($invitation->email)->queue(new TeamInvitation($view, $invitation));
    }

    /**
     * Create invitation for team
     * @param $team
     * @param $email
     * @param $invitedUser
     * @return mixed
     */
    public function createInvitation($team, $email, $invitedUser)
    {
        return $team->invitations()->create([
            'id' => Uuid::uuid4(),
            'user_id' => $invitedUser ? $invitedUser->id : null,
            'email' => $email,
            'token' => str_random(40),
        ]);
    }

    /**
     * Get the proper e-mail view for the given invitation.
     * @param Invitation $invitation
     * @return string
     */
    public function view(Invitation $invitation)
    {
        return $invitation->user_id
            ? 'settings.teams.emails.invitation-to-existing-user'
            : 'settings.teams.emails.invitation-to-new-user';
    }

    /**
     * Get the proper e-mail view for the given invitation.
     * @param Invitation $id
     * @return string
     */
    public function invitations($id)
    {
        return Invitation::where('team_id', $id)->get();
    }

    /**
     * Delete team member
     * @param $team
     * @param $member
     * @return mixed|void
     */
    public function deleteMember($team, $member)
    {
        //Get projects that has been opened for member
        $user = Auth::user();
        $projects = $user->projects()->whereHas('users', function ($query) use ($member) {
            $query->where('user_id', $member->id);
        })->get();

        //Delete member from projects
        if ($projects) {
            foreach ($projects as $project) {
                $project->users()->wherePivot('role', 'freelancer')->detach($member->id);
            }
        }

        //Delete member
        $team->users()->detach($member->id);
        event(new TeamMemberRemoved($team, $member));
    }
}
