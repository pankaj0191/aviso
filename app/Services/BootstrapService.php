<?php

namespace App\Services;


use Session;
use App\User;
use Laravel\Spark\Spark;
use App\ProfileDescription;
use Illuminate\Support\Str;
use App\Contracts\IBootstrapService;
use Illuminate\Support\Facades\Auth;

class BootstrapService implements IBootstrapService
{
    /**
     * @var User
     */
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Get user datas
     * @param $user
     * @return mixed
     */
    public function bootstrap($user)
    {
        $ownedTeam = $user->ownedTeams()->first();

        return [
            'nova' => $user->hasPermissionTo('Browes Nova'),
            'developer' => 'mykholys30@gmail.com',
            'support' => 'next club',
            'user' => $user->load('profiles.profileType', 'currentProfile.profileDescription'),
            'allowedNotify' => $user->emailNotifications,
            'isFreelancer' => isFreelancer($user),
            'currentRole' => currentRole($user),
            'isSubscribed' => isSubscribed($user),
            'currentPlan' => isSubscribed($user) ? $user->currentPlan() : [],
            'unread_comments' => $user->unreadComments->count(),
            'ownedTeam' => $ownedTeam ? $ownedTeam : '',
            'hasTeams' => $user->hasTeams(),
            'exitingMembers' => $ownedTeam ? $ownedTeam->users()->where('id', '<>', $user->id)->get() : [],
            'teamInvitations' => $ownedTeam ? $ownedTeam->invitations : [],
        ];
    }



    /**
     * Get user profile
     * @param $user
     * @return mixed
     */
    public function userStorage($user)
    {
        $projectFiles = $user->load(['currentProfile.subCategories.files', 'projects' => function ($q) use ($user) {
            $q->where('created_by', $user->id)->select('id', 'name', 'created_by')->with('projectAssets', 'finalFiles', 'revisions.proofs.issues.comments');
        }]);

        $sizeArray = [];
        $projectFileSize = 0;
        $projectAssetSize = 0;
        $projectFinalFileSize = 0;
        $issueFilesSize = 0;
        $commentFilesSize = 0;
        $subCategoryFilesSize = 0;


        if ($projectFiles->currentProfile->subCategories && count($projectFiles->currentProfile->subCategories) > 0) {
            // Current Profile get sub categories fiels
            foreach ($projectFiles->currentProfile->subCategories as $subCat) {
                // Subactegoies Files
                if (count($subCat->files) > 0) {
                    foreach ($subCat->files as $subFiles) {
                        // Size Files of sub categoreis
                        $subCategoryFilesSize += $subFiles->size;
                    }
                }
            }
        }

        if (count($projectFiles->projects) > 0) {
            // Projects
            foreach ($projectFiles->projects as $project) {
                if (count($project->finalFiles) > 0) {
                    // Final Files
                    foreach ($project->finalFiles as $finalFile) {
                        // Final Size
                        $projectFinalFileSize += $finalFile->size;
                    }
                }
                if (count($project->projectAssets) > 0) {
                    // Final Files
                    foreach ($project->projectAssets as $asset) {
                        // Asset Size
                        $projectAssetSize += $asset->size;
                    }
                }

                if (count($project->revisions) > 0) {
                    // Revisions
                    foreach ($project->revisions as $revision) {
                        if (count($revision->proofs) > 0) {
                            // Project Files
                            foreach ($revision->proofs as $proof) {
                                // Size of Project Files
                                $projectFileSize += $proof->projectFiles->size;
                                if (count($proof->issues) > 0) {
                                    // Issues
                                    foreach ($proof->issues as $issue) {
                                        if (count($issue->files) > 0) {
                                            // Issue Files
                                            foreach ($issue->files as $issueFile) {
                                                // Issue file size
                                                $issueFilesSize += $issueFile->size;
                                            }
                                        }
                                        if (count($issue->comments) > 0) {
                                            // Comments
                                            foreach ($issue->comments as $comment) {
                                                if (count($comment->files) > 0) {
                                                    // Comment Files
                                                    foreach ($comment->files as $commentFile) {
                                                        // Comment file size
                                                        $commentFilesSize += $commentFile->size;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $sizeArray['project_size'] = $projectFileSize;
        $sizeArray['project_asset_size'] = $projectAssetSize;
        $sizeArray['project_final_size'] = $projectFinalFileSize;
        $sizeArray['issue_size'] = $issueFilesSize;
        $sizeArray['comment_size'] = $commentFilesSize;
        $sizeArray['sub_category_size'] = $subCategoryFilesSize;
        $sizeArray['total'] = $projectFileSize + $projectAssetSize + $projectFinalFileSize + $issueFilesSize + $commentFilesSize + $subCategoryFilesSize;

        $sizeArray['total_formate'] = formatBytes($sizeArray['total']);
        return $sizeArray;
    }


   


    /**
     * Get user profile
     * @param $user
     * @return mixed
     */
    public function profile($user, $username)
    {
        return [
            'profile' => ProfileDescription::where('profile_id', $username)->first(),
            'userData' => $user->load('profiles', 'projectReviews.projectTimers'),
            'status_profile' => true,
            'total_projects' => $user->projects->where('pivot.role', Str::lower(currentRole($user)))->count(),
            'hours' => $user->projectTimers->sum('duration')
        ];
    }

    /**
     * Get User by ID
     * @param $id
     * @return mixed
     */
    public function getUserById($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Change project listing type for user
     * @param $user
     * @param $type
     * @return mixed
     */
    public function changeProjectsListingType($user, $type)
    {
        $user->projects_listing_type = $type;
        return $user->save();
    }

    /**
     * Get user recent data for project create
     * @param $user
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function getRecentDatas($user)
    {
        $projects = $user->projects;
        $clientEmails = [];
        $clientNames = [];
        $projectCompanies = [];
        $tempCompanies = [];

        if (count($projects)) {
            foreach ($projects as $project) {
                if ($project->autocomplete) {
                    //Client
                    $client = $project->users()->where('role', 'client')->first();

                    if ($client) {
                        //Client last project
                        $lastProject = $client->projects()->latest()->first();

                        if ($lastProject->autocomplete) {
                            //Client email
                            $email = (object) [
                                'value' => $client['email'],
                                'clientName' => $client['name'],
                                'projectCompany' => $lastProject['company'],
                                'id' => $lastProject['id']
                            ];
                            if (!in_array($email, $clientEmails)) {
                                $clientEmails[] = $email;
                            };

                            //Client name
                            $name = (object) [
                                'value' => $client['name'],
                                'clientEmail' => $client['email'],
                                'projectCompany' => $lastProject['company'],
                                'id' => $lastProject['id'],
                                'owner' => $user->id == $lastProject->created_by
                            ];
                            if (!in_array($name, $clientNames)) {
                                $clientNames[] = $name;
                            }
                        }
                    }

                    //Project Company
                    $company = [
                        'value' => $project->company,
                        'clientEmail' => $project->users()->where('role', 'client')->first()['email'],
                        'clientName' => $project->users()->where('role', 'client')->first()['name'],
                        'owner' => $user->id == $project->created_by,
                        'id' => $project->id
                    ];

                    if (!in_array(array_except($company, 'id'), $tempCompanies)) {
                        $tempCompanies[] = array_except($company, 'id');

                        $projectCompanies[] = (object) $company;
                    }
                }
            }
        }

        return [
            'clientEmails' => $clientEmails,
            'clientNames' => $clientNames,
            'projectCompanies' => $projectCompanies,
        ];
    }

    /**
     * Disable autocomplete for project
     * @param $data
     * @return mixed
     */
    public function disableAutocompleteData($user, $data)
    {
        $project = $user->projects->where('id', $data['id'])
            ->where('created_by', $user->id)->first();
        $project->autocomplete = 0;

        return $project->save();
    }

    /**
     * Get user active subscription and plan details
     * @param $user
     * @return array
     */
    public function getActiveSubscription($user)
    {
        $activeSubscription = $user->subscriptions->first();
        $currentPlan = $user->currentPlan();

        if ($currentPlan && $user->subscribedToPlan($currentPlan->stripe_plan_id, 'default')) {
            $result = [
                'subscription' => $activeSubscription,
                'plan' => $currentPlan,
                'ownedTeams' => $user->ownedTeams
            ];
            return $result;
        }

        return [];
    }
}
