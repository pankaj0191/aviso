<?php

namespace App\Services;

use App\Issue;
use App\Proof;
use App\Comment;
use App\Project;
use Carbon\Carbon;
use App\Events\IssuePosted;
use App\Events\CommentPosted;
use App\Contracts\IProofService;
use App\Events\IssueStatusChanged;
use App\Events\NotificationRealTime;
use Illuminate\Support\Facades\Auth;
use App\Contracts\INotificationService;
use App\Contracts\IUnreadCommentsService;

class ProofService implements IProofService
{
    protected $notifications;
    protected $model;
    protected $fileService;
    protected $unreadCommentsService;
    protected $slackService;

    /**
     * ProofService constructor.
     * @param INotificationService $notifications
     * @param IUnreadCommentsService $unreadCommentsService
     */
    public function __construct(INotificationService $notifications, IUnreadCommentsService $unreadCommentsService)
    {
        $this->notifications = $notifications;
        $this->unreadCommentsService = $unreadCommentsService;
        $this->model = new Proof();
        $this->fileService = new FileService();
        $this->slackService = new SlackService();
    }

    /**
     * @param $project_file
     * @return Proof|null
     */
    public function createProof($project_file)
    {
        try {
            $proof = Proof::store($project_file);
            if ($proof != null) {
                return $proof;
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param $revision_id
     * @return null
     */
    public function getByRevisionId($revision_id)
    {
        $proofs = Proof::getByRevisionId($revision_id);
        return $proofs;
    }

    /**
     * @param $version
     * @return null
     */
    public function getByRevisionVersion($version)
    {
        $proofs = Proof::getByRevisionVersion($version);
        return $proofs;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function saveProofState($data)
    {
        $proof = Proof::find($data['id']);

        if ($proof) {
            $proof->canvas_data = $data['canvas_data'];
            $proof->save();
            return $proof;
        }
    }

    /**
     * @param $proof_id
     * @param $status
     * @return mixed
     */
    public function changeProofStatus($proof_id, $status)
    {
        if ($proof_id > 0) {
            if ($status != '') {
                $proof = Proof::find($proof_id);
                $proof->status = $status;
                $proof->save();
                if ($status == 'approved') {
                    $issues = Issue::where('proof_id', $proof->id)->get();
                    if (count($issues) > 0) {
                        foreach ($issues as $key => $issue) {
                            /*  if ($issue->status != $status) { */
                            $issue->status = 'done';
                            $issue->save();
                            /* } */
                        }
                    }
                }

                return $proof;
            }
        }
    }

    /**
     * Change issue status
     * @param $issue_id
     * @param $status
     * @param $project_type
     * @return mixed
     */
    public function changeIssueStatus($issue_id, $status, $project_type)
    {
        if ($issue_id > 0) {
            if ($status != '') {
                $issue = Issue::find($issue_id);
                $issue->status = $status;
                $issue->save();
                $left_issues = Issue::where('proof_id', $issue->proof_id)->where('id', '!=', $issue->id)->get();
                $project = $issue->proof->revision->project;

                if ($project_type == 'website') {
                    $users = $project->users()->where('id', '<>', Auth::user()->id)->get();

                    //Get project freelancer
                    foreach ($users as $user) {
                        //Create notification for freelancer
                        $notification = $this->notifications->create($user, [
                            'icon' => 'fa fa-pencil-square-o',
                            'body' => 'Issue is Approved',
                            'type' => 'Approved Issue',
                            'action_text' => 'View Project',
                            'action_url' => 'proofer/' . $project->id . '/' . $issue->proof->revision->id . '/' . $issue->proof->id . '/' . $issue->id,
                            'company' => $project->company,
                            'project' => $project->name,
                            'proofer' => ''
                        ]);

                        event(new NotificationRealTime($notification));
                        event(new IssueStatusChanged($user, $project, $issue));
                    }
                }

                // Slack Notification
                if ($project->slack) {
                    $this->slackService->issueStatus(auth()->user(), $project, $issue);
                }

                $proof = $issue->proof;

                if (count($proof->issues()->where('status', 'todo')->get()) == 0) {
                    if (count($proof->issues()->where('status', 'review')->get()) == 0) {
                        return $this->changeProofStatus($issue->proof_id, 'approved');
                    }
                }

                return $issue;
            }
        }
    }

    /**
     * Create new issue
     * @param $data
     * @return Issue|Issue[]|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function createIssue($data)
    {
        $project_type = '';
        if (array_key_exists('project_type', $data)) {
            $project_type = $data['project_type'];
            $data = array_except($data, ['project_type']);
        }
        if (array_key_exists('id', $data)) {
            $issue = Issue::totalTime()->with('files')->with('user')->findOrFail($data['id']);
        } else {
            $issue = new Issue();
            $issue->group = $data['group'];
            $issue->label = $data['label'];
            $issue->proof_id = $data['proof_id'];
            $issue->time_video = $data['time_video'];
        }
        if (isset($data['description'])) {
            $issue->description = $data['description'];
        }
        if (isset($data['length_video'])) {
            $issue->length_video = $data['length_video'];
        }
        $issue->status = 'todo';
        $issue->user_id = Auth::user()->id;
        if (array_key_exists('owner_type', $data)) {
            $issue->owner_type = $data['owner_type'];
        }
        $issue->save();
        $issue = Issue::totalTime()->with('files')->with('user')->find($issue->id);

        $project = $issue->proof->revision->project;
        $project->updated_at = Carbon::now();
        $project->save();

        $users = $project->users()->where('id', '<>', Auth::user()->id)->get();
        foreach ($users as $user) {
            //Set viewed by user to 0 for project users
            $project->users()->updateExistingPivot($user->id, ['viewed_by_user' => 0]);
        }

        if ($project_type == 'website') {
            //Set proof status to issue
            $issue->proof()->update(['status' => 'issue']);

            //Get project freelancer
            foreach ($users as $user) {
                //Set viewed by user to 0 for project users
                $project->users()->updateExistingPivot($user->id, ['viewed_by_user' => 0]);

                //Create notification for project users
                $notification = $this->notifications->create($user, [
                    'icon' => 'el-icon-warning-outline',
                    'body' => 'New Issue is posted',
                    'type' => 'New Issue',
                    'action_text' => 'View Project',
                    'action_url' => 'proofer/' . $project->id . '/' . $issue->proof->revision->id . '/' . $issue->proof_id . '/' . $issue->id,
                    'company' => $project->company,
                    'project' => $project->name,
                    'proofer' => ''
                ]);


                event(new NotificationRealTime($notification));
                event(new IssuePosted($user, $project, $issue));
            }
        }

        // Slack Notification
        if ($project->slack) {
            $this->slackService->newIssue(auth()->user(), $project, $issue);
        }

        $issue['proof_status'] = $issue->proof->status;

        return $issue->load('timeTracker');
    }

    /**
     * Add new comment
     * @param $data
     * @return Comment|Comment[]|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function addComment($data)
    {
        if (array_key_exists('id', $data)) {
            if ($data['id'] > 0) {
                $comment = Comment::with('files')->with('user')->findOrFail($data['id']);
            }
        } else {
            $comment = new Comment();
        }
        $comment->issue_id = $data['issue_id'];
        $comment->description = $data['description'];
        $comment->user_id = Auth::user()->id;
        $comment->save();

        $project = Project::findOrFail($data['project_id']);
        $project->updated_at = Carbon::now();
        $project->save();

        $users = $project->users()->where('user_id', '<>', Auth::user()->id)->get();
        $data['comment_id'] = $comment->id;

        $new_comment = Comment::with('issue.proof')->find($comment->id);
        foreach ($users as $user) {
            //Set viewed by user to 0 for project users
            $project->users()->updateExistingPivot($user->id, ['viewed_by_user' => 0]);

            //Create user notifications
            $notification = $this->notifications->create($user, [
                'icon' => 'fa   fa-comments-o',
                'body' => Auth::user()->name . ' wrote a new comment',
                'type' => 'New Comment',
                'action_text' => 'View Comment',
                'action_url' => 'proofer/' . $project->id . '/' . $data['revision_id'] . '/' . $new_comment->issue->proof->id . '/' . $comment->issue->id . '/' . '#' . $comment->issue->group . '-' . $comment->id,
                'company' => $project->company,
                'project' => $project->name,
                'proofer' => ''
            ]);

            event(new NotificationRealTime($notification));
            //Set comment as unread for project users
            $this->unreadCommentsService->store($user, array_except($data, ['description']));
            event(new CommentPosted($user, $project, $new_comment, $data['revision_id']));
        }

        // Slack Notification
        if ($project->slack) {
            $this->slackService->newComment(auth()->user(), $project, $new_comment, $data['revision_id']);
        }

        $comment = Comment::with('files')->with('user')->find($comment->id);
        return $comment;
    }

    /**
     * Delete Issue
     * @param $issue_id
     * @return mixed
     */
    public function deleteIssue($issue_id)
    {
        $fileService = new FileService();
        $issue = Issue::findOrFail($issue_id);

        if (count($issue->comments)) {
            foreach ($issue->comments as $key => $comment) {
                if (count($comment->files)) {
                    foreach ($comment->files as $file) {
                        $fileService->deleteFile($file->id, 'comment');
                    }
                }

                $comment->delete();
            }
        }

        if (count($issue->files)) {
            foreach ($issue->files as $file) {
                $fileService->deleteFile($file->id, 'issue');
            }
        }

        return $issue->delete();
    }

    /**
     * Delete Comment
     * @param $comment_id
     * @return mixed
     */
    public function deleteComment($comment_id)
    {
        $fileService = new FileService();
        $comment = Comment::findOrFail($comment_id);

        if (count($comment->files)) {
            foreach ($comment->files as $file) {
                $fileService->deleteFile($file->id, 'comment');
            }
        }

        return $comment->delete();
    }

    /**
     * Delete Proof
     * @param $proofId
     * @return mixed
     */
    public function deleteProof($proofId)
    {
        $proof = $this->model->find($proofId);
        $issues = $proof->issues;

        //Deleting proof issues comments
        foreach ($issues as $issue) {
            $issue->comments()->delete();
        }

        //Deleting proof issues
        $proof->issues()->delete();

        //Deleting proof image from server
        $fileId = $proof->projectFiles->id;
        $this->fileService->deleteFile($fileId);

        //Deleting proof image from database
        $proof->projectFiles()->delete();

        //Delete proof
        return $proof->delete();
    }

    public function getIssues($user, $data)
    {
        $projects = $user->projects()
                        ->wherePivot('role', currentRole($user))
                        ->has('revisions')
                        ->orderBy('updated_at', 'desc')
                        ->filterIssues($user, $data)
                        ->totalTime()
                        ->get();


        if ($data->task > 0 && currentRole($user) == 'Freelancer') {

            foreach ($projects as $key => $project) {
                $projectOwner = $project->users()->where('user_id', $project->created_by)->first();
                if ($projectOwner && $projectOwner->id != $user->id) {
                    $projectTeam = $projectOwner->ownedTeams()->first();
                } else {
                    $projectTeam = '';
                }
                $project->team = $projectTeam ? $projectTeam->id : '';
            }

            return collect($projects)->where('team', $data->task);
        }

        return $projects;
    }

    public function assignIssue($data)
    {
        $issue = Issue::find($data['issue_id']);
        if ($issue->assign_id == $data['user_id']) {
            $issue->assign_id = null;
        } else {
            $issue->assign_id = $data['user_id'];
        }
        $issue->save();

        return $issue->load('assign', 'comments', 'unreadComments', 'user', 'files');
    }

    public function dueDate($data)
    {
        $issue = Issue::find($data['issue_id']);
        if ($issue->end_date == $data['date']) {
            $issue->end_date = null;
        } else {
            $issue->end_date = $data['date'];
        }
        $issue->save();

        return $issue->load('assign', 'comments', 'unreadComments', 'user', 'files');
    }

    public function addLiked($data)
    {
        $issue = Issue::find($data['issue_id']);
        $userId = auth()->user()->id;
        $likes = json_decode($issue->likes, true);
        if (is_array($likes) && in_array($userId, $likes)) {
            unset($likes[array_search($userId, $likes)]);
        } else {
            $likes[] = $userId;
        }
        $issue->likes = json_encode($likes);
        $issue->save();

        return $issue->load('assign', 'comments', 'unreadComments', 'user', 'files');
    }

    public function issuePriority($data)
    {
        $issue = Issue::find($data['issue_id']);
        if ($issue->tag == $data['status']) {
            $issue->tag = null;
        } else {
            $issue->tag = $data['status'];
        }
        $issue->save();

        return $issue->load('assign', 'comments', 'unreadComments', 'user', 'files');
    }
}
