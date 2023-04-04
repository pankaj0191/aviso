<?php

namespace App\Http\Controllers;

use App\Contracts\ITeamService;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    protected $teamService;

    public function __construct(ITeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    /**
     * Invite team member
     * @param Request $request
     * @return array
     */
    public function inviteMembers(Request $request)
    {
        try {
            $members = $this->teamService->inviteMembers($request->all());
            return ApiResponse::success('Invitation has been sent successfully', $members);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', $e . 'Something went wrong, please try again later');
        }
    }

    /**
     * invitations team member
     * @param Request $request
     * @return array
     */
    public function invitations($id)
    {
        try {
            $invitations = $this->teamService->invitations($id);
            if ($invitations) {
                return ApiResponse::success('', $invitations);
            } else {
                return ApiResponse::error('001', 'Error loading revision');
            }
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error loading revision');
        }
    }

    /**
     * Delete team member
     * @param $team
     * @param $member
     * @return array
     */
    public function deleteMember(Request $request, $team, $member)
    {
        try {
            $this->teamService->deleteMember($team, $member);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Something went wrong, please try again later');
        }
    }
}
