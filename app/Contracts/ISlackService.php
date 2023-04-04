<?php

namespace App\Contracts;

interface ISlackService
{
    public function slackNotification($channel);
    // public function newProject($user);
    public function newIssue($user, $project, $issue);
    public function issueStatus($user, $project, $issue);
    public function newComment($user, $project, $comment, $revisionId);
    public function newProject($user, $project, $revisionId);
    public function newCorrections($user, $project, $revisionId);
    public function finalFiles($user, $project, $revisionId);
    public function projectRated($user, $project, $revisionId);
    public function assignProjectToMember($user, $project, $revisionId, $assign);
    public function projectApproved($user, $project, $revisionId);
    public function pokeTeam($user, $project, $revisionId);
}
