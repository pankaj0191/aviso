<?php

namespace App\Services;

use App\User;
use App\Project;
use GuzzleHttp\Client;
use App\Contracts\ISlackService;
use Illuminate\Support\Facades\URL;
use App\Notifications\SlackNotification;


class SlackService implements ISlackService
{
    private $content;

    public function __construct()
    {
        // $this->model = $model;
    }

    public function slackNotification($channel)
    {
        $http = new Client();
        try {
            $response = $http->post('https://slack.com/api/chat.postMessage', [
                'headers' => [
                    'Authorization' => 'Bearer xoxb-403437707252-2290992920291-Gu3An6gbqEBdHxNylwAI0yJw'
                ],
                'form_params' => [
                    'text' => $this->content,
                    'channel' => $channel
                ]
            ]);
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if ($e->getCode() === 400) {
                return response()->json('Invalid Request. Please enter a client_id or client_secret.', $e->getCode());
            } else if ($e->getCode() === 401) {
                return response()->json('Your credentials are incorrect. Please try again', $e->getCode());
            }
            return response()->json('Something went wrong on the server.', $e->getCode());
        }
    }

    private function slackMessage($projectName, $userID, $content, $icon, $title, $body, $link, $mentionTo)
    {
        $this->content = $userID . ' ';
        $this->content .= $content;
        $this->content .= " <" . $link . "|" . $projectName . ">\n> ";
        $this->content .= $icon  . ' ' . $title;
        if ($body) {
            $this->content .= "\n> " . $body;
        }
        return $this->content;
    }

    public function newProject($user, $project, $issue)
    {
        // Send URL
        $projectName = $project->name;
        $channel = $project->slack;
        // User Create
        $userID = "<" . URL::to('profile/' . $user->current_profile_id) . "|" . $user->name . ">";
        $content = 'added new project';

        // Blockquote title
        $icon = ':tada:';
        $title = 'New Project';
        $body = null;
        $link = URL::to('proofer/' . $project->id . '/' . $revisionId);
        $mentionTo = 'Shane Long | Freelancer';

        $this->slackMessage($projectName, $userID, $content, $icon, $title, $body, $link, $mentionTo);
        $this->slackNotification($channel);
    }
    public function newIssue($user, $project, $issue)
    {
        // Send URL
        $projectName = $project->name;
        $channel = $project->slack;
        // User Create
        $userID = "<" . URL::to('profile/' . $user->current_profile_id) . "|" . $user->name . ">";
        $content = 'added new issue';

        // Blockquote title
        $icon = ':information_source:';
        $title = 'New Issue';
        $body = $issue->description;
        $link = URL::to('proofer/' . $project->id . '/' . $issue->proof->revision->id . '/' . $issue->proof_id . '/' . $issue->id);
        $mentionTo = 'Shane Long | Freelancer';

        // $user->notify(new SlackNotification($projectName, $webhook, $userID, $content, $icon, $title, $body, $link, $mentionTo));
        $this->slackMessage($projectName, $userID, $content, $icon, $title, $body, $link, $mentionTo);
        $this->slackNotification($channel);
    }
    public function issueStatus($user, $project, $issue)
    {
        // Send URL
        $projectName = $project->name;
        $channel = $project->slack;
        // User Create
        $userID = "<" . URL::to('profile/' . $user->current_profile_id) . "|" . $user->name . ">";
        $content = 'approved an issue';

        // Blockquote title
        $icon = ':raised_hands:';
        $title = 'Issue Approved';
        $body = $issue->description;
        $link = URL::to('proofer/' . $project->id . '/' . $issue->proof->revision->id . '/' . $issue->proof_id . '/' . $issue->id);
        $mentionTo = 'Shane Long | Freelancer';

        // $user->notify(new SlackNotification($projectName, $webhook, $userID, $content, $icon, $title, $body, $link, $mentionTo));
        $this->slackMessage($projectName, $userID, $content, $icon, $title, $body, $link, $mentionTo);
        $this->slackNotification($channel);
    }

    public function newCorrections($user, $project, $revisionId)
    {
        // Send URL
        $projectName = $project->name;
        $channel = $project->slack;

        // User Create
        $userID = "<" . URL::to('profile/' . $user->current_profile_id) . "|" . $user->name . ">";
        $content = 'added new correction';

        // Blockquote title
        $icon = ':information_source:';
        $title = 'New Correction';
        $body = null;
        $link = URL::to('proofer/' . $project->id . '/' . $revisionId);
        $mentionTo = 'Shane Long | Freelancer';

        $this->slackMessage($projectName, $userID, $content, $icon, $title, $body, $link, $mentionTo);
        $this->slackNotification($channel);
    }

    public function finalFiles($user, $project, $revisionId)
    {
        // Send URL
        $projectName = $project->name;
        $channel = $project->slack;

        // User Create
        $userID = "<" . URL::to('profile/' . $user->current_profile_id) . "|" . $user->name . ">";
        $content = 'uploaded files';

        // Blockquote title
        $icon = ':white_check_mark:';
        $title = 'Comeplted | Download Final Files';
        $body = null;
        $link = URL::to('proofer/' . $project->id . '/' . $revisionId);
        $mentionTo = 'Shane Long | Freelancer';

        $this->slackMessage($projectName, $userID, $content, $icon, $title, $body, $link, $mentionTo);
        $this->slackNotification($channel);
    }
    public function newComment($user, $project, $comment, $revisionId)
    {
        // Send URL
        $projectName = $project->name;
        $channel = $project->slack;
        // User Create
        $userID = "<" . URL::to('profile/' . $user->current_profile_id) . "|" . $user->name . ">";
        $content = 'wrote a new comment';

        // Blockquote title
        $icon = ':speech_balloon:';
        $title = 'New Comment';
        $body = $comment->description;
        $link = URL::to('proofer/' . $project->id . '/' . $revisionId . '/' . $comment->issue->proof->id . '/' . $comment->issue->id . '/' . '#' . $comment->issue->group . '-' . $comment->id);
        $mentionTo = 'Shane Long | Freelancer';

        // $user->notify(new SlackNotification($projectName, $webhook, $userID, $content, $icon, $title, $body, $link, $mentionTo));
        $this->slackMessage($projectName, $userID, $content, $icon, $title, $body, $link, $mentionTo);
        $this->slackNotification($channel);
    }
    public function projectRated($user, $project, $revisionId)
    {
        // Send URL
        $projectName = $project->name;
        $channel = $project->slack;
        // User Create
        $userID = "<" . URL::to('profile/' . $user->current_profile_id) . "|" . $user->name . ">";
        $content = 'rated a project';

        // Blockquote title
        $icon = ':star:';
        $title = 'Project Rated';
        $body = null;
        $link = URL::to('proofer/' . $project->id . '/' . $revisionId);
        $mentionTo = 'Shane Long | Freelancer';

        $this->slackMessage($projectName, $userID, $content, $icon, $title, $body, $link, $mentionTo);
        $this->slackNotification($channel);
    }
    public function assignProjectToMember($user, $project, $revisionId, $assign)
    {
        // Send URL
        $projectName = $project->name;
        $channel = $project->slack;
        // User Create
        $userID = "<" . URL::to('profile/' . $user->current_profile_id) . "|" . $user->name . ">";
        $content = 'assign project to ' . $assign->name . ' project';

        // Blockquote title
        $icon = ':heavy_plus_sign:';
        $title = 'Added member to Project';
        $body = null;
        $link = URL::to('proofer/' . $project->id . '/' . $revisionId);
        $mentionTo = 'Shane Long | Freelancer';

        $this->slackMessage($projectName, $userID, $content, $icon, $title, $body, $link, $mentionTo);
        $this->slackNotification($channel);
    }

    public function pokeTeam($user, $project, $revisionId)
    {
        // Send URL
        $projectName = $project->name;
        $channel = $project->slack;
        // User Create
        $userID = "<" . URL::to('profile/' . $user->current_profile_id) . "|" . $user->name . ">";
        $content = 'Poke notification to team';

        // Blockquote title
        $icon = ':point_up_2:';
        $title = 'You have been poked to this project ' . $project->name . ' by ' . $user->name;
        $body = null;
        $link = URL::to('proofer/' . $project->id . '/' . $revisionId);
        $mentionTo = 'Shane Long | Freelancer';

        $this->slackMessage($projectName, $userID, $content, $icon, $title, $body, $link, $mentionTo);
        $this->slackNotification($channel);
    }

    public function projectApproved($user, $project, $revisionId)
    {
        // Send URL
        $projectName = $project->name;
        $channel = $project->slack;
        // User Create
        $userID = "<" . URL::to('profile/' . $user->current_profile_id) . "|" . $user->name . ">";
        $content = 'approved project';

        // Blockquote title
        $icon = ':white_check_mark:';
        $title = 'Project Approved';
        $body = null;
        $link = URL::to('proofer/' . $project->id . '/' . $revisionId);
        $mentionTo = 'Shane Long | Freelancer';

        $this->slackMessage($projectName, $userID, $content, $icon, $title, $body, $link, $mentionTo);
        $this->slackNotification($channel);
    }
}
