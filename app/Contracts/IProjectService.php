<?php

namespace App\Contracts;

interface IProjectService
{
    /**
     * @param $user
     * @return mixed
     */
    public function getProjectsByUser($user);
    /**
     * @param $user
     * @return mixed
     */
    public function getCategories($user, $active);
    /**
     * @param $user
     * @return mixed
     */
    public function activeCategory($data, $user);
    /**
     * @param $user
     * @return mixed
     */
    public function activeProjectType($data);
    /**
     * @param $user
     * @return mixed
     */
    public function createProjectType($data, $user);
    /**
     * @param $user
     * @return mixed
     */
    public function editProjectType($data, $user, $id);
    /**
     * @param $user
     * @return mixed
     */
    public function uploadFileProjectType($data, $user, $id);
    /**
     * @param $user
     * @return mixed
     */
    public function deleteFileProjectType($id);
    /**
     * @param $user
     * @return mixed
     */
    public function destroyProjectType($id);
    /**
     * @param array $data
     * @return mixed
     */
    public function createProject(array $data);

    /**
     * Save Creative Brief
     * @param array $data
     * @return mixed
     */
    public function saveCreativeBrief(array $data);

    /**
     * Get Creative Brief
     * @param $id
     * @return mixed
     */
    public function getCreativeBrief($id);

    /**
     * @param array $data
     * @return mixed
     */
    public function createSendProject(array $data);

    /**
     * @param $project_id
     * @return mixed
     */
    public function getActiveRevision($project_id);

    /**
     * @param $project_id
     * @return mixed
     */
    public function getRevisionVersions($project_id);

    /**
     * @param $project_id
     * @param $user_type
     * @return mixed
     */
    public function sendProject($project_id, $user_type);

    /**
     * @param array $data
     * @return mixed
     */
    public function approveProject(array $data);

    /**
     * @param $project_id
     * @return mixed
     */
    public function submitCorrections($project_id);

    /**
     * @param $project_id
     * @return mixed
     */
    public function deleteProject($project_id);

    /**
     * @param $project_id
     * @return mixed
     */
    public function bulkDeleteProject($data);

    /**
     * @param $data
     * @return mixed
     */
    public function sendfinalFiles($data);

    /**
     * @param $projectId
     * @return mixed
     */
    public function getDetails($projectId);

    /**
     * @param $projectId
     * @param $data
     * @return mixed
     */
    public function updateProject($projectId, $data);
    /**
     * @param $projectId
     * @param $data
     * @return mixed
     */
    public function updateProjectBudget($projectId, $data);
    /**
     * @param $projectId
     * @param $data
     * @return mixed
     */
    public function updateProjectDescription($projectId, $data);
    /**
     * @param $projectId
     * @param $data
     * @return mixed
     */
    public function submitProjectSummary($projectId, $data);
    /**
     * @param $projectId
     * @param $data
     * @return mixed
     */
    public function invitationEmail($projectId, $data);
    /**
     * @param $token
     * @return mixed
     */
    public function invitationToken($token);
    /**
     * @param $token
     * @return mixed
     */
    public function invitationAccept($token);
    /**
     * @param $token
     * @return mixed
     */
    public function invitationDecline($token);
    /**
     * @param $token
     * @return mixed
     */
    public function fetchClients($data);
    /**
     * @param $token
     * @return mixed
     */
    public function fetchFreelancer($data);
    /**
     * @param $projectId
     * @param $data
     * @return mixed
     */
    public function updateRate($projectId, $data);

    /**
     * @param $projectId
     * @param $status
     * @return mixed
     */
    public function changeStatus($projectId, $status);

    /**
     * @param $lastRevision
     * @return mixed
     */
    public function getIssues($lastRevision);

    /**
     * @param $project
     * @param $collaborators
     * @return mixed
     */
    public function registerCollaborators($project, $collaborators);

    /**
     * @param array $data
     * @return mixed
     */
    public function registerUser(array $data);

    /**
     * @param $project_id
     * @param $revison_id
     * @return mixed
     */
    public function loadInitialRevision($project_id, $revision_id);

    /**
     * @param $data
     * @return mixed
     */
    public function addTeamMember($data);

    /**
     * @param $projectId
     * @param $memberId
     * @return mixed
     */
    public function deleteTeamMember($projectId, $memberId);

    /**
     * Get project final files
     * @param $projectId
     * @return array
     */
    public function getFinalFiles($projectId);

    /**
     * @param $projectId
     * @param $data
     * @return mixed
     */
    public function deleteInvitation($projectId, $invitationId);

    /**
     * @param $projectId
     * @return mixed
     */
    public function pokeNotify($projectId);
}