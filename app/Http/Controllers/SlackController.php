<?php

namespace App\Http\Controllers;

use App\User;
use App\Project;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Contracts\ISlackService;
use App\Contracts\IRevisionService;
use Illuminate\Support\Facades\URL;
use App\Notifications\SlackNotification;

class SlackController extends Controller
{

    private $slackService, $revisionService;

    public function __construct(ISlackService $slackService, IRevisionService $revisionService)
    {
        $this->slackService = $slackService;
        $this->revisionService = $revisionService;
    }

    /**
     * Oauth Slack and store user data.
     *
     * @return \Illuminate\Http\Response
     */
    public function oauth(Request $request)
    {
        $http = new Client();
        try {
            $response = $http->post(config('services.slack.oauth_endpoint'), [
                'form_params' => [
                    'client_id' => config('services.slack.client_id'),
                    'client_secret' => config('services.slack.client_secret'),
                    'code' => $request->code,
                ]
            ]);
            $user = User::find(auth()->user()->id);
            $user->slack = $response->getBody();
            $user->save();
            return redirect('/dashboard');
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if ($e->getCode() === 400) {
                return response()->json('Invalid Request. Please enter a client_id or client_secret.', $e->getCode());
            } else if ($e->getCode() === 401) {
                return response()->json('Your credentials are incorrect. Please try again', $e->getCode());
            }
            return response()->json('Something went wrong on the server.', $e->getCode());
        }
    }

    public function connect(Request $request){
        $project = Project::where('project_hash', $request->text)->first();
        if ($project) {
            $project->slack = $request->channel_id;
            $project->save();
        } else {
            return 'Wrong Hash code';
        }
        $revision = $this->revisionService->getLastRevision($project->id);
        $url = URL::to('/proofer/' . $project->id . '/' . $revision->id);
        return "<" . $url . "|" . $project->name . ">";
    }
}
