<?php

namespace App\Services;

use App\User;
use App\Profile;
use App\Project;
use App\Category;
use Carbon\Carbon;
use App\Instruction;
use App\ProfileType;
use App\SubCategory;
use App\CategoryFile;
use App\ProjectAssets;
use App\ProjectInvitation;
use Illuminate\Support\Str;
use App\Helpers\ApiResponse;
use App\Events\ProjectCreated;
use App\Services\SlackService;
use App\Events\CustomerInvited;
use App\Events\ProjectApproved;
use App\Contracts\IProjectService;
use App\EmailNotificationSettings;
use App\Events\FinalFilesUploaded;
use App\Events\InvitePepoleByLink;
use App\Events\NewRevisionCreated;
use Illuminate\Support\Facades\DB;
use App\Contracts\IRevisionService;
use App\Events\CorrectionsSubmitted;
use App\Events\NotificationRealTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Services\ClientRequestService;
use App\Contracts\INotificationService;
use Illuminate\Support\Facades\Storage;


class ProjectService implements IProjectService
{
    private $revisionService;


    private $model;

    protected $notifications;

    protected $slackService;

    public function __construct(IRevisionService $revisionService, Project $model, INotificationService $notifications)
    {
        $this->revisionService = $revisionService;
        $this->model = $model;
        $this->notifications = $notifications;
        $this->slackService = new SlackService();
    }

    /**
     * Get user projects
     * @param $user
     * @return array|mixed
     */
    public function getProjectsByUser($user)
    {
        $projects = [];
        $projectTeam = '';

        $userProjects = $user->projects()->has('revisions')->orderBy('updated_at', 'desc')->wherePivot('role', currentRole($user))->get();
        $ownedTeam = $user->ownedTeams()->first();

        if ($ownedTeam) {
            $teamMembers = $ownedTeam->users()->where('id', '<>', $user->id)->pluck('id');
        } else {
            $teamMembers = [];
        }

        if (count($userProjects)) {
            foreach ($userProjects as $key => $project) {
                $lastRevision = $this->revisionService->getLastRevision($project->id);
                $issues = $this->getIssues($lastRevision);
                $userRole = currentRole(Auth::user());
                $unreadComments = $project->unreadComments()->where('user_id', $user->id)->count();
                $viewedByUser = $project->users()->where('user_id', $user->id)->pluck('viewed_by_user');
                $firstOpen = $project->users()->where('user_id', $user->id)->pluck('first_open');
                $projectUsers = $project->users()->pluck('user_id');
                $projectTeamMembers = $project->users()->whereIn('id', $teamMembers)->get();
                $client = $project->users()->where('role', 'client')->first();
                $freelancer = $project->users()->where('role', 'freelancer')->first();
                $collabCount = $project->users()->where('role', 'collaborator')->count();
                $collaborators = $project->users()->where('role', 'collaborator')->get();
                $projectOwner = $project->users()->where('user_id', $project->created_by)->first();
                $projectTimer = $this->model->getActiveTimeTracker($project->id);
                $totalTracker = $this->model->getTotalTimeTracker($project->id);
                if ($projectOwner && $projectOwner->id != $user->id && ($userRole == 'Freelancer' || $userRole == 'Agency')) {
                    $projectTeam = $projectOwner->ownedTeams()->first();
                } else {
                    $projectTeam = '';
                }
                $projects[$key] = [
                    'id' => $project->id,
                    'owner' => $project->created_by,
                    'budget' => $project->budget,
                    'freelancer_hrate' => $freelancer ? $freelancer->pivot->hourly_rate : 0,
                    'name' => $project->name,
                    'project_hash' => $project->project_hash,
                    'creative_brief' => $project->creative_brief,
                    'rate' => $project->rate,
                    'review' => $project->review,
                    'active_revision' => $lastRevision,
                    'company' => '',
                    'status' => $project->status,
                    'type' => $project->type,
                    'start' => $project->start,
                    'end' => $project->end,
                    'created_at' => $project->created_at,
                    'total_issues' => $issues['totalIssues'],
                    'solved_issues' => $issues['solvedIssues'],
                    'review_issues' => $issues['reviewIssues'],
                    'percentage' => $issues['percentage'],
                    'role' => $userRole,
                    'unreadComments' => $unreadComments,
                    'viewedByUser' => $viewedByUser[0],
                    'firstOpen' => $firstOpen[0],
                    'websiteURI' => $project->website_url,
                    'users' => $projectUsers,
                    'teamMembers' => $projectTeamMembers,
                    'client' => $client,
                    'collabCount' => $collabCount,
                    'collaborators' => $collaborators,
                    'active' => ($projectOwner && isSubscribed($projectOwner) || ($freelancer && isSubscribed($freelancer) && $projectOwner->pivot->role == 'client')) ? true : false,
                    'created_role' =>  $projectOwner->pivot->role,
                    'team' => $projectTeam ? $projectTeam->name : '',
                    'finalFiles' => $this->getFinalFiles($project->id),
                    'timeTracker' => $projectTimer,
                    'totalTracker' => $totalTracker
                ];
            }
            return $projects;
        }
        return [];
    }

    /**
     * Get user projects
     * @param $user
     * @return array|mixed
     */
    public function getCategories($user, $data)
    {
        if ($data['worker'] > 0) {
            $user = $data['worker'];
        } else {
            $user = $user->currentProfile->id;
        }
        $categories = Category::where('active', true)->with(['profiles' => function  ($q) use($user) {
            $q->where('profile_id', $user);
        }])->latest()->get();
        if ($data['active']) {
            $subCategories = SubCategory::where('profile_id', $user)->with('files')->latest()->get();
        } else{
            $subCategories = SubCategory::where('profile_id', $user)->with('files')->where('active', true)->latest()->get();
        }
        return [
            'categories' => $categories,
            'subCategories' => $subCategories->load('category'),
        ];
    }

    /**
     * Get user projects
     * @param $user
     * @return array|mixed
     */
    public function activeCategory($data, $user)
    {
        $category = Category::find($data['id']);
        $category->profiles()->detach($user->currentProfile->id);
        $category->profiles()->attach($user->currentProfile->id, ['active' => $data['status']]);
        // $user->categories()->detach($data['id']);
        // $user->categories()->attach($data['id'], ['active' => $data['status']]);
        return $category->load(['profiles' => function  ($q) use($user) {
            $q->where('profile_id', $user->currentProfile->id);
        }]);
    }

    /**
     * Get user projects
     * @param $user
     * @return array|mixed
     */
    public function activeProjectType($data)
    {
        $subCategory = SubCategory::find($data['id']);
        $subCategory->active = $data['status'];
        $subCategory->save();

        return $subCategory->load('category');
    }

    /**
     * Get user projects
     * @param $user
     * @return array|mixed
     */
    public function deleteFileProjectType($id)
    {
        $categoryFile = CategoryFile::find($id);
        Storage::disk('spaces')->delete($categoryFile->file);
        Storage::disk('spaces')->delete($categoryFile->image);
        return CategoryFile::destroy($id);
    }

    /**
     * Get user projects
     * @param $user
     * @return array|mixed
     */
    public function destroyProjectType($id)
    {
        SubCategory::destroy($id);
    }

    /**
     * Get user projects
     * @param $user
     * @return array|mixed
     */
    public function editProjectType($data, $user, $id)
    {

        $subCategory = SubCategory::find($id);
        $subCategory->category_id = $data['category_id'];
        $subCategory->name = $data['name'];
        $subCategory->size_type = $data['size_type'];
        $subCategory->width = $data['width'];
        $subCategory->height = $data['height'];
        $subCategory->active = $data['active'] ? 1 : 0;
        $subCategory->slug = Str::slug($data['name'])  . '-' . Str::random(6);

        if($data->hasfile('image')) {
            Storage::disk('spaces')->delete($subCategory->image);

            $subCategory->image = $data->file('image')->store('sub-categories/image', 'spaces');
        }

        $subCategory->save();

        return $subCategory->load('category');
    }
    /**
     * Get user projects
     * @param $user
     * @return array|mixed
     */
    public function uploadFileProjectType($data, $user, $id)
    {
        if ($data->has('id')) {
            $categoryFile = CategoryFile::find($data['id']);
        } else {
            $categoryFile = new CategoryFile();
        }
        $categoryFile->sub_category_id = $id;
        
        if($data->hasfile('image')) {
            Storage::disk('spaces')->delete($categoryFile->image);
            $categoryFile->image = $data->file('image')->store('sub-categories/files/image', 'spaces');
        }
        
        if($data->hasfile('template')) {
            $categoryFile->name = $data->file('template')->getClientOriginalName();
            $categoryFile->size = $data->file('template')->getSize();
            Storage::disk('spaces')->delete($categoryFile->file);
            $categoryFile->file = $data->file('template')->store('sub-categories/files/file', 'spaces');
        }

        $categoryFile->save();

        return $categoryFile;
    }

    /**
     * Get user projects
     * @param $user
     * @return array|mixed
     */
    public function createProjectType($data, $user)
    {
        $subCategory = SubCategory::create([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'size_type' => $data['size_type'],
            'profile_id' => $user->currentProfile->id,
            'width' => $data['width'],
            'height' => $data['height'],
            'active' => $data['active'] ? 1 : 0,
            'slug' => Str::slug($data['name'])  . '-' . Str::random(6),
            'image' => $data['image'],
        ]);
        return $subCategory->load('category', 'files');
    }

    /**
     * Create new project
     * @param array $data
     * @return Project|mixed|null|string
     */
    public function createProject(array $data)
    {
        //Create project
        $project = $this->model->create([
            'name' => $data['name'],
            'project_hash' => Str::random(40),
            'video_url' => array_key_exists('video_url', $data) ? $data['video_url'] : '',
            'type' => array_key_exists('type', $data) ? $data['type'] : 'design',
            'website_url' => array_key_exists('website_url', $data) ? $data['website_url'] : '',
            'status' => 'draft',
            'job_number' => hexdec(substr(uniqid(), 0, 6)),
            'created_by' => Auth::user()->id,
            'width' => $data['width'],
            'height' => $data['height'],
        ]);

        //Create project revision
        $project->revisions()->create([
            'version' => 1,
            'status_revision' => 'draft',
        ]);

        if (isset($data['worker']) && $data['worker'] && strtolower(currentRole(Auth::user())) == 'client') {
            $profile = Profile::where('id', $data['worker'])->first();

            $project->projectInvitations()->create([
                'user_id' => $profile->user_id,
                'token' => Str::random(40),
                'role' => $profile->profileType->value,
                'permission' => 'editor',
                'email' => $profile->user->email
            ]);

            $project->users()->attach([
                // ['user_id' => $profile->user_id, 'role' => $profile->profileType->value, 'hourly_rate' => Auth::user()->currentProfile->profileDescription->hourly_rate, 'review_project' => $data['review_project']],
                ['user_id' => Auth::user()->id, 'role' => strtolower(currentRole(Auth::user())), 'hourly_rate' => 0, 'review_project' => false ],
            ]);
        } else {
            //Create project users
            $project->users()->attach([
                ['user_id' => Auth::user()->id, 'role' => strtolower(currentRole(Auth::user())), 'hourly_rate' => Auth::user()->currentProfile->profileDescription->hourly_rate, 'review_project' => $data['review_project']],
            ]);
        }

        if ($data['category'] != 'custom') {
            $subCategory = SubCategory::where('slug', $data['category'])->first();
            $project->subCategories()->attach([
                ['project_id' => $project->id, 'sub_category_id' => $subCategory->id, 'copy_path' => null],
            ]);
        }

        $project->projectInvitations()->create([
            'user_id' => null,
            'token' => Str::random(40),
            'role' => 'client',
            'permission' => 'editor',
            'email' => ''
        ]);

        $project->projectInvitations()->create([
            'user_id' => null,
            'token' => Str::random(40),
            'role' => 'collaborator',
            'permission' => 'editor',
            'email' => ''
        ]);

        return [
            'project' => $project
        ];
    }

    /**
     * Create new project
     * @param array $data
     * @return Project|mixed|null|string
     */
    public function createProject1(array $data)
    {
        $clientData['client_name'] = $data['client_name'];
        $clientData['email'] = $data['email'];
        $client = $this->registerUser($clientData);
        if ($client) {
            //Create project
            $project = $this->model->create([
                'name' => $data['name'],
                'project_hash' => Str::random(40),
                'video_url' => array_key_exists('video_url', $data) ? $data['video_url'] : '',
                'type' => array_key_exists('type', $data) ? $data['type'] : 'design',
                'website_url' => array_key_exists('website_url', $data) ? $data['website_url'] : '',
                'status' => 'draft',
                'company' => $data['company'],
                'job_number' => hexdec(substr(uniqid(), 0, 6)),
                'created_by' => Auth::user()->id,
                'start' => isset($data['daterange'][0]) ? $data['daterange'][0] : '',
                'end' => isset($data['daterange'][1]) ? $data['daterange'][1] : '',
            ]);

            //Create project revision
            $project->revisions()->create([
                'version' => 1,
                'status_revision' => 'draft',
            ]);

            //Create project users
            $project->users()->attach([
                ['user_id' => Auth::user()->id, 'role' => strtolower(currentRole(Auth::user())), 'hourly_rate' => $data['hourly_rate'], 'review_project' => $data['review_project']],
                ['user_id' => $client['user']->id, 'role' => 'client', 'hourly_rate' => 0, 'review_project' => false]
            ]);

            //Create project collaborators
            if (array_key_exists('collaborators', $data) && count($data['collaborators'])) {
                $this->registerCollaborators($project, $data['collaborators']);
            }

            $user = $client['user'];

            if ($client['newUser']) {
                //Send welcome email if client is newly registered
                $token = app('auth.password.broker')->createToken($user);

                event(new CustomerInvited($user, $project, $token));
            } else {
                //Send new project email if client is already registered
                $revision = $this->revisionService->getLastRevision($project->id);

                event(new ProjectCreated($user, $project, $revision));
            }

            return [
                'newClient' => $client['newUser'],
                'client' => $client['user'],
                'project' => $project
            ];
        }
        return null;
    }

    /**
     * Save Creative Brief
     * @param array $data
     * @return mixed
     */
    public function saveCreativeBrief(array $data)
    {
        return $this->model->where('id', $data['project_id'])
            ->update(['creative_brief' => $data['creative_brief']]);
    }

    /**
     * Get Creative Brief
     * @param $id
     * @return mixed
     */
    public function getCreativeBrief($id)
    {
        $project = $this->model->findOrFail($id);

        return $project->creative_brief;
    }

    /**
     * Get project active revision
     * @param $project_id
     * @return mixed|void
     */
    public function getActiveRevision($project_id)
    {
        $activeRevision = Project::getActiveRevision($project_id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function createSendProject(array $data)
    {
        //        $project = Project::store($data);
        //        $message = Mail::to($request->email)->send(new ProjectSent($project));
        //        return $message;
    }

    /**
     * @param $project_id
     * @return mixed|string
     */
    public function getRevisionVersions($project_id)
    {
        $revisions = Project::getRevisionVersions($project_id);
        return $revisions;
    }

    /**
     * Send new revision
     * @param $project_id
     * @param $user_type
     * @return mixed|null
     */
    public function sendProject($project_id, $user_type)
    {
        $project = Project::findOrFail($project_id);
        if ($project) {
            $revision = $this->revisionService->getLastRevision($project->id);

            if ($revision) {
                $this->revisionService->changeRevisionStatus($revision->id, 'revision');
            }

            $users = $project->users()->where('id', '<>', Auth::user()->id)->get();

            foreach ($users as $user) {
                //Set viewed by user to 0 for project users
                $project->users()->updateExistingPivot($user->id, ['viewed_by_user' => 0]);

                $firstOpen = $project->users()->where('user_id', $user->id)->pluck('first_open')[0];

                //Create site notifications for project users
                $notification = $this->notifications->create($user, [
                    'icon' => 'fa fa-pencil-square-o',
                    'body' => Auth::user()->name . ' moved project to your Inbox',
                    'type' => 'Project In Progress',
                    'action_text' => 'View Project',
                    'action_url' => 'dashboard/create/' . currentRole(Auth::user()) == 'Client' ? 'request' : 'project' . '/' . $project->type . '/' . $project->id . '/summary',
                    'company' => currentRole(Auth::user()),
                    'project' => $project->name,
                    'proofer' => ''
                ]);

                event(new NotificationRealTime($notification));

                if (!$firstOpen) {
                    event(new NewRevisionCreated($user, $project, $revision));
                }
            }
            return $revision;
        }
        return null;
    }

    /**
     * Registering new user for project
     * @param array $data
     * @return User|mixed|null
     */
    public function registerUser(array $data)
    {
        if ($this->getUserByEmail($data['email'])) {
            if (\Auth::user()->email == $data['email']) {
                return null;
            } else {
                $user = $this->getUserByEmail($data['email']);
                $newUser = false;
            }
        } else {
            $user = new User();
            $user->name = $data['client_name'];
            $user->password = bcrypt(Str::random(7));
            $user->email = $data['email'];
            $user->verified = 1;
            $user->save();

            $emailNotifications = new EmailNotificationSettings();
            $user->emailNotifications()->save($emailNotifications);
            $newUser = true;
        }
        return [
            'user' => $user,
            'newUser' => $newUser
        ];
    }

    public function getUserByEmail($email)
    {
        if ($email != '') {
            $user = User::where('email', $email)->first();
            return $user;
        }
        return null;
    }

    /**
     * Approve project
     * @param array $data
     * @return array|mixed
     */
    public function approveProject(array $data)
    {
        $project = $this->model->where('id', $data['project_id'])->first();

        if ($project) {
            //Set viewed by user to 0 for project users
            $projectUsers = $project->users()->where('user_id', '<>', Auth::user()->id)->get();
            foreach ($projectUsers as $user) {
                $project->users()->updateExistingPivot($user->id, ['viewed_by_user' => 0]);
            }
            //Change project status
            $project->status = 'approved';
            $project->save();

            //Change last revision status
            $revision = $this->revisionService->getLastRevision($project->id);
            $version = $this->revisionService->changeRevisionStatus($revision->id, 'approved');

            //Get project freelancers
            $freelancers = $project->users()->where('role', '<>','client')->get();

            if (count($version->proofs) == 0) {
                $version->delete();
                $version->save();
            }
            if ($version) {
                foreach ($freelancers as $freelancer) {
                    //Create site notification for freelancers
                    $notification = $this->notifications->create($freelancer, [
                        'icon' => 'fa fa-check',
                        'body' => Auth::user()->name . ' approved project',
                        'type' => 'Approved',
                        'action_text' => 'View Project',
                        'action_url' => 'proofer/' . $project->id . '/' . $revision->id,
                        'company' => currentRole(Auth::user()),
                        'project' => $project->name,
                        'proofer' => ''
                    ]);

                    event(new NotificationRealTime($notification));
                    event(new ProjectApproved($freelancer, $project, $revision));
                }
                // Slack Notification
                if ($project->slack) {
                    $this->slackService->projectApproved(auth()->user(), $project, $revision->id);
                }
                return ApiResponse::success('Project approved successfully', ['client' => Auth::user()]);
            } else {
                return ApiResponse::error('001', 'This revision has already been approved. You can safely close your browser window and wait for future revisions');
            }
        }
        return ApiResponse::error('001', 'There has been an error sending the email');
    }

    /**
     * Submit Corrections
     * @param $project_id
     * @return mixed|null
     */
    public function submitCorrections($project_id)
    {
        $project = Project::findOrFail($project_id);
        if ($project) {
            //Set viewed by user to 0 for project users
            $projectUsers = $project->users()->where('user_id', '<>', Auth::user()->id)->get();
            foreach ($projectUsers as $user) {
                $project->users()->updateExistingPivot($user->id, ['viewed_by_user' => 0]);
            }

            //Change project status
            $project->status = 'progress';
            $project->save();

            //Change last revision status
            $revision = $this->revisionService->getLastRevision($project->id);
            $revision = $this->revisionService->changeRevisionStatus($revision->id, 'progress');

            $users = $project->users()->where('role', '<>','client')->get();
            foreach ($users as $user) {
                //Create site notifications for project users
                $notification = $this->notifications->create($user, [
                    'icon' => 'fa fa-clock-o',
                    'body' => Auth::user()->name . ' added new corrections',
                    'type' => 'New Corrections',
                    'action_text' => 'View Project',
                    'action_url' => 'proofer/' . $project->id . '/' . $revision->id,
                    'company' => currentRole(Auth::user()),
                    'project' => $project->name,
                    'proofer' => ''
                ]);

                event(new NotificationRealTime($notification));
                event(new CorrectionsSubmitted($user, $project, $revision));
            }

            // Slack Notification
            if ($project->slack) {
                $this->slackService->newCorrections(auth()->user(), $project, $revision->id);
            }

            // Get project owner
            $owner = $project->users()->where('id', $project->created_by)->first();

            return [
                'revision' => $revision,
                'owner' => $owner
            ];
        }
        return null;
    }

    /**
     * Saving and sending final files
     * @param $data
     * @return array|null
     */
    public function sendfinalFiles($data)
    {
        $project = $this->model->where('id', $data['project_id'])->first();

        if (array_key_exists('owner_type', $data)) {
            $links = [];
        } else {
            $links = $data['links'];

            //Store project final links
            $project->finalLinks()->delete();
            foreach ($links as $link) {
                $project->finalLinks()->create(['url' => $link]);
            }
        }

        //Change project status;
        $project->status = 'completed';
        $project->save();

        //Change last revision status
        $revision = $this->revisionService->getLastRevision($project->id);
        $revision = $this->revisionService->changeRevisionStatus($revision->id, 'completed');

        //Get project client
        $client = $project->users()->where('role', 'client')->first();

        //Get project owner
        $owner = $project->users()->where('id', $project->created_by)->first();

        //Set viewed by user to 0 for project client
        $project->users()->updateExistingPivot($client->id, ['viewed_by_user' => 0]);

        //Create site notification for client
        $notification = $this->notifications->create($client, [
            'icon' => 'fa fa-upload',
            'body' => Auth::user()->name . ' uploaded files',
            'type' => 'Completed',
            'action_text' => 'View Project',
            'action_url' => 'proofer/' . $project->id . '/' . $revision->id,
            'company' => currentRole(Auth::user()),
            'project' => $project->name,
            'proofer' => ''
        ]);

        event(new NotificationRealTime($notification));
        event(new FinalFilesUploaded($client, $project, $links, $revision));
        
        // Slack Notification
        if ($project->slack) {
            $this->slackService->finalFiles(auth()->user(), $project, $revision->id);
        }

        return [
            'client' => $client,
            'owner' => $owner,
        ];
    }

    /**
     * Delete project
     * @param $project_id
     * @return mixed
     */
    public function deleteProject($project_id)
    {
        $project = $this->model->with('revisions.proofs')->findOrFail($project_id);
        $project->projectTimers()->delete();
        return $project->delete();
    }

    /**
     * Delete project
     * @param $project_id
     * @return mixed
     */
    public function bulkDeleteProject($data)
    {
        foreach ($data['selected'] as $project_id) {
            $project = $this->model->with('revisions.proofs')->findOrFail($project_id);
            $project->projectTimers()->delete();
            $project->delete();
        }
        return null;
    }

    /**
     * Get project details
     * @param $projectId
     * @return mixed
     */
    public function getDetails($projectId)
    {
        $project = $this->model->where('id', $projectId)->with('users', 'instructions', 'projectAssets', 'projectInvitations.user', 'subCategories.files')->first();

        if ($project) {
            $owner = $project->users()->where('user_id', $project->created_by)->first();
            $client = $project->users()->where('role', 'client')->first();
            $freelancer = $project->users()->where('role', 'freelancer')->first();

            return [
                'project' => $project,
                'client' => $client,
                'revision_id' => $this->revisionService->getLastRevision($project->id)->id,
                'active' => (isSubscribed($owner) || (isSubscribed($freelancer) && $owner->pivot->role == 'client')) ? true : false
            ];
        }
        return null;
    }

    /**
     * Get all Clients has profile client
     * @param 
     * @return mixed
     */
    public function fetchClients($data)
    {
        return $clients = User::filterClient($data)->whereHas('profiles.profileType', function($q) {
            return $q->where('value', 'client');
        })->get();
    }

    /**
     * Get all Clients has profile client
     * @param 
     * @return mixed
     */
    public function fetchFreelancer($data)
    {
        return $clients = User::filterClient($data)->whereHas('profiles.profileType', function($q) {
            return $q->where('value', 'freelancers');
        })->get();
    }

    /**
     * Update project data
     * @param $projectId
     * @param $data
     * @return mixed
     */
    public function updateProject($projectId, $data)
    {
        $project = $this->model->find($projectId);
        //Create project users
        $project->users()->detach(Auth::user()->id);
            $project->users()->attach([
                ['user_id' => Auth::user()->id, 'role' => strtolower(currentRole(Auth::user())), 'hourly_rate' => Auth::user()->currentProfile->profileDescription->hourly_rate, 'review_project' => $data['review_project']],
            ]);
        $project->update($data);
        return $project->load('users', 'instructions', 'projectAssets', 'projectInvitations.user', 'subCategories.files');
    }

    /**
     * Update project data budget
     * @param $projectId
     * @param $data
     * @return mixed
     */
    public function updateProjectBudget($projectId, $data)
    {
        $project = $this->model->find($projectId);
        if ($data['budget_type'] == 'hourly') {
            $data['budget'] = 0;
            $project->users()->updateExistingPivot(Auth::user(), ['hourly_rate' => $data->hourly_rate]);
            $project->update($data->except('hourly_rate'));
        } elseif ($data['budget_type'] == 'budget') {
            $project->users()->updateExistingPivot(Auth::user(), ['hourly_rate' => 0]);
            $project->update($data->except('hourly_rate'));
        } else {
            $data['budget'] = 0;
            $project->users()->updateExistingPivot(Auth::user(), ['hourly_rate' => 0]);
            $project->update($data->except('hourly_rate'));
        }
        return $project->load('users', 'instructions', 'projectAssets', 'projectInvitations.user', 'subCategories.files');
    }

    /**
     * Update project data Description
     * @param $projectId
     * @param $data
     * @return mixed
     */
    public function updateProjectDescription($projectId, $data)
    {
        $project = $this->model->with('instructions', 'subCategories.category')
            ->where('id', $projectId)->first();

        if ($data->has('instructions', 'project_id')) {
            $this->createInstruction($data, $projectId);
        }

        if ($data->has('extention')) {
            $ex = $data->all();
            if ($data->has('extentionAdobe')) {
                $arr = array_push($ex['extention'], $ex['extentionAdobe']);
            }
            $project->update([
                'file_types' => json_encode($ex['extention']),
            ]);
        }

        if (isset($data['creative_brief']) && isset($data['includeText']) && $data['includeText']) {
            if (!$data->has('status')) {
                $project->update([
                    'creative_brief' => $data['creative_brief'],
                    'active_stepper' => 3,
                ]);
            }
        } else if (isset($data['includeText']) && !$data['includeText']) {
            if (!$data->has('status')) {
                $project->update([
                    'active_stepper' => 3,
                ]);
            }
        } else if ($data['step'] == 'assets') {
            if (!$data->has('status')) {
                $project->update([
                    'assets_text' => $data['assets_text'],
                    'active_stepper' => 4,
                ]);
            }

            $projectAssets = ProjectAssets::where('project_id', $projectId)->where('type', 'photos')->get();
            if ($projectAssets->count() > 0) {
                foreach ($projectAssets as $projectAsset) {
                    Storage::disk(config('filesystems.default'))->delete($projectAsset->thumb_path);
                }
                $projectAssets->each->delete();
            }

            foreach ($data['photos'] as $key => $photo) {
                // return dd($data['photo']);
                $image = 'projects/' . Carbon::now()->year . '/' . $project->name  . '-' . $project->id . '/' . 'Design Request' . '/' . $project->name . 'StockPhoto' . ($key + 1) . '.jpeg';
                $projectAssets = ProjectAssets::create([
                    'project_id' => $projectId,
                    'url' => json_encode($photo),
                    'thumb_path' => $image,
                    'type' => 'photos',
                ]);
                $unsplash  = new Photo();
                $link      = $unsplash->download($photo['id']);
                $content = file_get_contents($link);
                Storage::disk(config('filesystems.default'))->put($image, $content, 'public');
            }
        }


        // return [
        //     'project' => $project,
        // ];
        return $project->load('users', 'projectInvitations.user', 'projectAssets', 'projectInvitations.user', 'subCategories.files');
    }

    /**
     * Update project data Description
     * @param $projectId
     * @param $data
     * @return mixed
     */
    public function submitProjectSummary($projectId, $data)
    {     
        $project = $this->updateProjectDescription($projectId, $data);
        
        //Send new project email if client is already registered
        $revision = $this->revisionService->getLastRevision($project->id);
        
        

        if($data['draft']) {
            $project->status = 'draft';
            $project->save();
        } else {
            if ($revision) {
                $this->revisionService->changeRevisionStatus($revision->id, 'revision');
            }
        }

        $users = $project->users()->where('id', '<>', Auth::user()->id)->get();

        foreach ($users as $user) {
            //Set viewed by user to 0 for project users
            $project->users()->updateExistingPivot($user->id, ['viewed_by_user' => 0]);

            $firstOpen = $project->users()->where('user_id', $user->id)->pluck('first_open')[0];

            //Create site notifications for project users
            $notification = $this->notifications->create($user, [
                'icon' => 'fa fa-pencil-square-o',
                'body' => Auth::user()->name . ' moved project to your Inbox',
                'type' => 'Project In Progress',
                'action_text' => 'View Project',
                'action_url' => 'proofer/' . $project->id . '/' . $revision->id,
                'company' => currentRole(Auth::user()),
                'project' => $project->name,
                'proofer' => ''
            ]);

            event(new NotificationRealTime($notification));

            if (!$firstOpen) {
                event(new NewRevisionCreated($user, $project, $revision));
            }
        }

        // Slack Notification
        if ($project->slack) {
            $this->slackService->newProject(auth()->user(), $project, $revision->id);
        }

        $projectInvitations = $project->projectInvitations;

        foreach($projectInvitations as $inv) {
            if ($inv->email) {
                event(new InvitePepoleByLink($inv, $project, $revision));
            } 
             if ($inv->user_id) {
                    //Create site notification for freelancers
                    $notification = $this->notifications->create($inv->user, [
                        'icon' => 'el-icon-s-promotion',
                        'body' => 'Hello ' . Auth::user()->name . ' You have been invited to (' . $project->name . ') project',
                        'type' => 'Invitation',
                        'action_text' => 'Accept Project',
                        'action_url' => 'dashboard/invitation/' . $inv->token,
                        'project' => $project->name,
                        'proofer' => '',
                        'company' => currentRole(Auth::user())
                    ]);
                event(new NotificationRealTime($notification));
            }
        }        

        return $project;

    }

    public function createInstruction($data, $projectId)
    {
        $instruction = [];
        if (isset($data['update']) && $data['update']) {
            Instruction::where('project_id', $projectId)->update([
                'status' => 0
            ]);
            foreach ($data['instructions'] as $instruction) {
                $instruction =  Instruction::where('project_id', $projectId)->where('id', $instruction)->update([
                    'status' => 1
                ]);
            }
        } else {
            Instruction::where('project_id', $data['project_id'])->delete();
            foreach ($data['instructions'] as $instruction) {
                if (strlen($instruction['text']) > 4) {
                    $instruction = Instruction::create([
                        'text' => $instruction['text'],
                        'project_id' => $data['project_id']
                    ]);
                }
            }
        }
        return $instruction;
    }

    /**
     * Update project data
     * @param $projectId
     * @param $data
     * @return mixed
     */
    public function invitationToken($token)
    {
        return ProjectInvitation::where('token', $token)->with('project.owner', 'user')->first();
    }

    /**
     * Update project data
     * @param $projectId
     * @param $data
     * @return mixed
     */
    public function invitationDecline($token)
    {
        return ProjectInvitation::where('token', $token)->delete();
    }

    /**
     * Update project data
     * @param $projectId
     * @param $data
     * @return mixed
     */
    public function invitationAccept($token)
    {
        $projectInv = ProjectInvitation::where('token', $token)->with('project.owner', 'user')->first();
        if ($projectInv) {
            if (Auth::user()->id != $projectInv->project->created_by) {
                $projectInv->project->users()->detach(Auth::user()->id);
                // Delete user from project if the invitation role is client
                if ($projectInv->role == 'client') {
                    $projectInv->project->users()->wherePivot('role', 'client')
                    ->wherePivot('user_id', '<>' ,Auth::user()->id)
                    ->detach();
                }
                
                $projectInv->project->users()->attach([
                    ['user_id' => Auth::user()->id, 'role' => $projectInv->role, 'hourly_rate' => Auth::user()->currentProfile->profileDescription->hourly_rate, 'review_project' => true],
                ]);

                $projectInv->delete();
            }
        }
    }

    /**
     * Delete Project invitation by project id and invitation id
     */
    public function deleteInvitation($projectId, $invitationId)
    {
        $project = $this->model->find($projectId);
        $project->projectInvitations()->where('id', $invitationId)->delete();

        return $project->load('users', 'instructions', 'projectAssets', 'projectInvitations.user', 'subCategories.files');
    }

    /**
     * Update project data
     * @param $projectId
     * @param $data
     * @return mixed
     */
    public function invitationEmail($projectId, $data)
    {
        if ($data->email != Auth::user()->email) {
            $user = User::where('email', $data->email)->with(['profiles' => function ($query) {
                $query->where('profile_type_id', ProfileType::where('value', 'client')->first()->id);
            }])->first();

            $project = $this->model->find($projectId);
            $revision = $this->revisionService->getLastRevision($project->id);

            if ($user && $user->profiles->count() > 0) {
 
                if ($user->projectInvitations->where('project_id', $projectId)->count() == 0) {
                    if ($data['role'] == 'client') {
                        ProjectInvitation::where('project_id', $project->id)->where('role', 'client')->delete();
                        $project->projectInvitations()->create([
                            'user_id' => $user->profiles->count() > 0 ? $user->id : null,
                            'token' => Str::random(40),
                            'role' => $data['role'],
                            'permission' => 'editor',
                            'email' => $user->email
                        ]);
                    } else {
                        $project->projectInvitations()->create([
                            'user_id' => $user->profiles->count() > 0 ? $user->id : null,
                            'token' => Str::random(40),
                            'role' => $data['role'],
                            'permission' => 'commenter',
                            'email' => $user->email
                        ]);
                    }
                    
                    if ($data->type == 'proof' && $user->id) {
                        $projectInvitations = $project->projectInvitations;
                        foreach($projectInvitations as $inv) {
                            if ($inv->email) {
                                event(new InvitePepoleByLink($inv, $project, $revision));
                            } 
                            if ($inv->user_id) {
                                    //Create site notification for freelancers
                                    $notification = $this->notifications->create($inv->user, [
                                        'icon' => 'el-icon-s-promotion',
                                        'body' => 'Hello ' . Auth::user()->name . ' You have been invited to (' . $project->name . ') project',
                                        'type' => 'Invitation',
                                        'action_text' => 'Accept Project',
                                        'action_url' => 'dashboard/invitation/' . $inv->token,
                                        'project' => $project->name,
                                        'proofer' => '',
                                        'company' => currentRole(Auth::user())
                                    ]);
                                event(new NotificationRealTime($notification));
                            }
                        }
                    }

                    return [
                        'project' => $project->load('users', 'instructions', 'projectAssets', 'projectInvitations.user', 'subCategories.files'),
                        'message' => 'Invited client to project successfully',
                        'status' => 1
                    ];
                }
        
                return [
                    'project' => '',
                    'message' => 'This client is already invited',
                    'status' => 0
                ];
            } elseif (!$user) {
                if ($data['role'] == 'client') {
                    ProjectInvitation::where('project_id', $project->id)->where('role', 'client')->delete();

                    $project->projectInvitations()->create([
                        'token' => Str::random(40),
                        'role' => $data['role'],
                        'permission' => 'editor',
                        'email' => $data->email
                    ]);
                } else {
                    $project->projectInvitations()->create([
                        'token' => Str::random(40),
                        'role' => $data['role'],
                        'permission' => 'commenter',
                        'email' => $data->email
                    ]);
                }
                if ($data->type == 'proof') {
                    $projectInvitations = $project->projectInvitations;
                    foreach($projectInvitations as $inv) {
                        if ($inv->email) {
                            event(new InvitePepoleByLink($inv, $project, $revision));
                        } 
                    }
                }

                return [
                    'project' => $project->load('users', 'instructions', 'projectAssets', 'projectInvitations.user', 'subCategories.files'),
                    'message' => 'Invited client to project successfully',
                    'status' => 1
                ];
            } else {
                return [
                    'project' => '',
                    'message' => 'This user is exist and dosn\'t have client project',
                    'status' => 0
                ]; 
            }
        } else {
            return [
                'project' => '',
                'message' => 'This user is the same user you are loged in now!',
                'status' => 0
            ];
        }
    }

    /**
     * Update project data
     * @param $projectId
     * @param $data
     * @return mixed
     */
    public function updateRate($projectId, $data)
    {
        //Get project freelancers
        $project = $this->model->find($projectId);
        $project->update($data);
        $freelancers = $project->users()->with('emailNotifications')->where('role', '<>','client')->get();


        //Change last revision status
        $revision = $this->revisionService->getLastRevision($project->id);

        foreach ($freelancers as $freelancer) {
            if ($freelancer->emailNotifications['review_project']) {
                //Create site notification for freelancers
                $notification = $this->notifications->create($freelancer, [
                    'icon' => 'fa fa-star',
                    'body' => Auth::user()->name . ' has been rated (' . $project->name . ')',
                    'type' => 'Rate',
                    'action_text' => 'View Project',
                    'action_url' => 'profile/' . $freelancer->id,
                    'company' => currentRole(Auth::user()),
                    'project' => $project->name,
                    'proofer' => ''
                ]);
            }
            event(new NotificationRealTime($notification));
        }

        // Slack Notification
        if ($project->slack) {
            $this->slackService->projectRated(auth()->user(), $project, $revision->id);
        }
        return $project;
    }

    /**
     * Change project status
     * @param $projectId
     * @param $status
     * @return mixed
     */
    public function changeStatus($projectId, $status)
    {
        $project = $this->model->where('id', $projectId)->first();
        if ($project) {
            $project->finalFiles()->delete();
            if ($status == 'progress') {
                //Set viewed by user to 0 for project users
                $users = $project->users()->where('user_id', '<>', Auth::user()->id)->get();
                foreach ($users as $user) {
                    $project->users()->updateExistingPivot($user->id, ['viewed_by_user' => 0]);
                }
            }

            if ($project->type == 'design') {
                //Change last revision status

                $lastRevision = $this->revisionService->getLastRevision($project->id);
                $lastRevision->status_revision = $status;
                $lastRevision->save();

                // Change project status
                return $project->update(['status' => $status]);
            } elseif ($project->type == 'website') {
                //Change last revision status
                $lastRevision = $this->revisionService->getLastRevision($project->id);
                $lastRevision->status_revision = ($status == 'hold') ? 'hold' : $status;
                $lastRevision->save();

                // Change project status
                return $project->update(['status' => ($status == 'hold') ? 'hold' : $status]);
            }
        }

        return null;
    }

    /**
     * Get project issues
     * @param $lastRevision
     * @return array|mixed
     */
    public function getIssues($lastRevision)
    {
        $proofs = $lastRevision->proofs;
        $totalIssues = 0;
        $solvedIssues = 0;
        $reviewIssues = 0;
        $percentage = 0;

        foreach ($proofs as $proof) {
            $totalIssues += $proof->issues->count();
            $solvedIssues += $proof->issues()->where('status', 'done')->count();
            $reviewIssues += $proof->issues()->where('status', 'review')->count();
        }

        if ($solvedIssues > 0) {
            $percentage = round(($solvedIssues / $totalIssues) * 100);
        }

        return [
            'totalIssues' => $totalIssues,
            'solvedIssues' => $solvedIssues,
            'reviewIssues' => $reviewIssues,
            'percentage' => $percentage,
        ];
    }

    /**
     * Create Collaborators for project
     * @param $project
     * @param $collaborators
     * @return mixed
     */
    public function registerCollaborators($project, $collaborators)
    {
        $collabs = [];

        foreach ($collaborators as $key => $collaborator) {
            if ($this->getUserByEmail($collaborator)) {
                $user = $this->getUserByEmail($collaborator);

                //Send new project email if collaborator is already registered
                $revision = $this->revisionService->getLastRevision($project->id);

                event(new ProjectCreated($user, $project, $revision));
            } else {
                $user = $this->registerUser([
                    'client_name' => 'Collaborator',
                    'email' => $collaborator
                ]);

                $user = $user['user'];

                //Send welcome email if collaborator is newly registered
                $token = app('auth.password.broker')->createToken($user);

                event(new CustomerInvited($user, $project, $token));
            }

            $collabs[$key]['user_id'] = $user->id;
            $collabs[$key]['role'] = 'collaborator';
        }

        return $project->users()->attach($collabs);
    }

    /**
     * Load last revision
     * @param $project_id
     * @param $revision_id
     * @return array|mixed|null
     */
    public function loadInitialRevision($project_id, $revision_id)
    {
        $result = [];

        $user = Auth::user();
        $project = $this->model->where('id', $project_id)->first();
        $ownedTeam = $user->ownedTeams()->first();
        if ($ownedTeam) {
            $teamMembers = $ownedTeam->users()->where('id', '<>', $user->id)->pluck('id');
        } else {
            $teamMembers = [];
        }
        if ($project) {
            $project->users()->updateExistingPivot(Auth::user()->id, ['viewed_by_user' => 1]);
            $owner = $project->users()->where('user_id', $project->created_by)->first();
            $freelancer = $project->users()->where('role', 'freelancer')->first();
            $result['owner'] = $owner;
            $result['project_status'] = $project->status;
            $result['project_hash'] = $project->project_hash;
            $result['rate'] = $project->rate;
            $result['created_at'] = $project->created_at;
            $result['job_number'] = $project->job_number;
            $result['project_type'] = $project->type;
            $result['websiteURI'] = $project->website_url;
            $result['clients'] = $project->users()->whereIn('role', ['client', 'collaborator'])->get();
            $result['teamMembers'] = $project->users()->whereIn('id', $teamMembers)->get();
            $result['exitingUsers'] = $project->users()->pluck('email');
            $result['freelancers'] = $project->userRoleFreelancer->load('emailNotifications');
            $result['users'] = $project->users;
            $result['agencies'] = $project->userRoleAgency->load('emailNotifications');
            $result['firstOpen'] = $project->users()->where('user_id', Auth::user()->id)->first()->pivot->first_open;
            $result['active'] = isSubscribed($owner) || (isSubscribed($freelancer) && $owner->pivot->role == 'client') ? true : false;
            $result['creative_brief'] = $project->creative_brief;
            $result['instructions'] = $project->instructions;
            $result['sub_categories'] = $project->subCategories()->with('category')->get();
            $result['project_assets'] = $project->projectAssets;
            $result['project_photos'] = $project->projectPhotos;
            $result['width'] = $project->width;
            $result['height'] = $project->height;
            $result['file_types'] = $project->file_types;
            $result['id'] = $project->id;
            $result['name'] = $project->name;
            $result['company'] = '';
            $result['timeTracker'] = $this->model->getActiveTimeTracker($project->id);
            $result['totalTracker'] = $this->model->getTotalTimeTracker($project->id);
            $result['project_invitations'] = $project->projectInvitations->load('user');
            $revisionVersions = $this->getRevisionVersions($project_id);
            if ($revisionVersions) {
                foreach ($revisionVersions as $key => $version) {
                    $result['versions'][$key]['id'] = $version->id;
                    $result['versions'][$key]['status'] = $version->status_revision;
                    $result['versions'][$key]['value'] = $version->version;
                    $result['versions'][$key]['label'] = 'Revision Round ' . $version->version;
                }
            }
            if ($revision_id > 0) {
                $revision = $this->revisionService->getRevisionById($revision_id);
                if ($revision) {
                    $issues = $this->getIssues($revision);
                    $result['last_revision'] = $revision;
                    $result['total_issues'] = $issues['totalIssues'];
                    $result['review_issues'] = $issues['reviewIssues'];
                    $result['solved_issues'] = $issues['solvedIssues'];
                    $result['percentage'] = $issues['percentage'];
                }
            } else {
                $revision = $this->revisionService->getLastRevision($project_id);
                if ($revision) {
                    $issues = $this->getIssues($revision);
                    $result['last_revision'] = $revision;
                    $result['total_issues'] = $issues['totalIssues'];
                    $result['solved_issues'] = $issues['solvedIssues'];
                    $result['percentage'] = $issues['percentage'];
                }
            }
            //Update first open field
            $project->users()->updateExistingPivot(Auth::user()->id, ['first_open' => 0]);

            return $result;
        }
        return null;
    }

    /**
     * Add team member to project
     * @param $data
     * @return mixed|null
     */
    public function addTeamMember($data)
    {
        $user = User::find($data['member_id']);
        $project = $this->model->where('id', $data['project_id'])->first();
        $revision = $this->revisionService->getLastRevision($project->id);
        $projectUser = DB::table('project_user')->where('project_id', $data['project_id'])->where('hourly_rate', '>', 0)->first();

        if ($project) {
            //Create site notifications for project users
            $notification = $this->notifications->create($user, [
                'icon' => 'el-icon-s-promotion',
                'body' => 'You have been added to ' . $project->name,
                'type' => 'Project Team',
                'action_text' => 'View Project',
                'action_url' => '#',
                'company' => currentRole(Auth::user()),
                'project' => $project->name,
                'proofer' => ''
            ]);

            // Slack Notification
            if ($project->slack) {
                $this->slackService->assignProjectToMember(auth()->user(), $project, $revision->id, $user);
            }

            event(new NotificationRealTime($notification));
            
            $project->users()->attach([
                [
                    'role' => 'freelancer',
                    'user_id' => $data['member_id'],
                    'hourly_rate' => $projectUser ? $projectUser->hourly_rate : 0,
                    'review_project' => $user->emailNotifications->review_project
                ]
            ]);
            return $project->load('users', 'instructions', 'projectAssets', 'projectInvitations.user', 'subCategories.files');
        }
        return null;
    }

    /**
     * Remove team member from project
     * @param $projectId
     * @param $memberId
     * @return mixed|null
     */
    public function deleteTeamMember($projectId, $memberId)
    {
        $project = $this->model->where('id', $projectId)->first();

        if ($project) {
            $user = $project->users()->wherePivot('role', '<>','client')->where('id', $memberId)->get();
            if (count($user) == 0) {
                return 'You can not remove client from project';
            }
            return $project->users()->detach($memberId);
        }
        return $project->load('users', 'instructions', 'projectAssets', 'projectInvitations.user', 'subCategories.files');
    }

    /**
     * Get project final files
     * @param $projectId
     * @return array
     */
    public function getFinalFiles($projectId)
    {
        $project = $this->model->where('id', $projectId)->first();
        return [
            'links' => $project->finalLinks()->pluck('url'),
            'files' => $project->finalFiles,
        ];
    }

    public function pokeNotify($projectId) {
        $project = $this->model->where('id', $projectId)->first();
        $revision = $this->revisionService->getLastRevision($project->id);
        $projectUsers = $project->users()->wherePivot('role', 'client')->orWhere('role', 'collaborator')->get();

        if ($project) {
            // dd($projectUsers, $project, $revision);

            foreach ($projectUsers as $projectUser) {
                if ($projectUser->id == Auth::user()->id) {
                    continue;
                }
                //Create site notifications for project users
                $notification = $this->notifications->create($projectUser, [
                    'icon' => 'el-icon-thumb',
                    'body' => 'You have been poked to this project ' . $project->name . ' by ' . Auth::user()->name,
                    'type' => 'Poke Team',
                    'action_text' => 'View Project',
                    'action_url' => 'proofer/' . $project->id . '/' . $revision->id,
                    'company' => currentRole(Auth::user()),
                    'project' => $project->name,
                    'proofer' => ''
                ]);
    
                // Slack Notification
                if ($project->slack) {
                    $this->slackService->pokeTeam(auth()->user(), $project, $revision->id);
                }
            }

            event(new NotificationRealTime($notification));
        }
    }
}
