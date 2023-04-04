<?php

namespace App\Http\Controllers;

use App\User;
use ZipArchive;
use App\Project;
use App\Revision;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Contracts\IProjectService;
use App\Events\FinalFilesUploaded;
use Illuminate\Support\Facades\DB;
use App\Contracts\IRevisionService;
use App\Contracts\IBootstrapService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProjectRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UpdateDescriptionProject;
use App\Http\Requests\UpdateBudgetProjectRequest;

class ProjectController extends Controller
{
    private $projectService;
    private $revisionService;
    private $bootstrapService;

    public function __construct(IProjectService $projectService, IRevisionService $revisionService, IBootstrapService $bootstrapService)
    {
        $this->projectService = $projectService;
        $this->revisionService = $revisionService;
        $this->bootstrapService = $bootstrapService;
    }

    public function downloadZip($project_id)
    {
        // $result = $this->projectService->getFinalFiles($projectId);
        $project = Project::with('finalFiles')->where('id', $project_id)->first();
        $client = $project->users()->where('role', 'client')->first();
        $revision = $this->revisionService->getLastRevision($project->id);
        // return dd($project);

        $zip = new ZipArchive(); // Load zip library   
        $zip_name =  $project->name . "-project.zip";
        if ($zip->open($zip_name, ZIPARCHIVE::CREATE) !== TRUE) {
            // Opening zip file to load files  
            $error .= "* Sorry ZIP creation failed at this time";
        }
        // foreach($post['files'] as $file)  
        // {   
        $links = [];
        foreach ($project->finalFiles as $final) {
            $data = Storage::disk(config('filesystems.default'))->get(DIRECTORY_SEPARATOR . $final->path);
            Storage::disk('public')->put(DIRECTORY_SEPARATOR . $final->path, $data);
            $links = $final->path;
            $zip->addFile(public_path('storage' . DIRECTORY_SEPARATOR . $final->path), 'prooflo-' . $project->type . '-' . $project->name . '/' . pathinfo($final->path)['basename']);
            Storage::disk('public')->delete(DIRECTORY_SEPARATOR . $final->path);
        }
        // }  
        $zip->close();
        if (file_exists($zip_name)) {
            // push to download the zip 
            $headers = array(
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'attachment; filename="' . $zip_name . '"'
            );
            event(new FinalFilesUploaded($client, $project, $links = [], $revision, $zip_name));
            return response()->download($zip_name, $zip_name, $headers)->deleteFileAfterSend(true);
        }
    }

    public function index()
    {
        $user = \Auth::user();

        try {
            return $this->projectService->getProjectsByUser($user);
        } catch (\Exception $e) {
            report($e);
            return [];
        }
    }

    public function store(ProjectRequest $request)
    {
        $data = $request->all();
        try {
            DB::beginTransaction();
            $result = $this->projectService->createProject($data);
            DB::commit();
            if ($result) {
                $data = [
                    'project_id' => $result['project']->id,
                    'revision_id' => $result['project']->getActiveRevision($result['project']->id)->id,
                    'user_id' => Auth::user()->id,
                ];
                return ApiResponse::success('Project saved successfully', $data);
            } else {
                return ApiResponse::error('001', 'This email address is associated with your freelancer account in this project. Please use a different client email address');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return ApiResponse::error('001', 'Error occurred. Please try again later');
        }
    }

    public function getCategories(Request $request)
    {
        $user = \Auth::user();

        try {
            return $this->projectService->getCategories($user, $request);
        } catch (\Exception $e) {
            report($e);
            return [];
        }
    }

    public function activeCategory(Request $request)
    {
        $user = \Auth::user();

        try {
            return $this->projectService->activeCategory($request, $user);
        } catch (\Exception $e) {
            report($e);
            return [];
        }
    }

    public function activeProjectType(Request $request)
    {

        try {
            return $this->projectService->activeProjectType($request);
        } catch (\Exception $e) {
            report($e);
            return [];
        }
    }

    /**
     * Save project creative brief
     * @param Request $request
     * @return array
     */
    public function createProjectType(Request $request)
    {
        $user = \Auth::user();

        $validatedData = $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'image' => 'required',
        ]);

        try {
            $result = $this->projectService->createProjectType($request, $user);
            return ApiResponse::success('Project type created successfully!', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error occurred. Please try again later');
        }
    }

    /**
     * save create project type image
     * @param Request $request
     * @return array
     */
    public function createProjectTypeImage(Request $request)
    {
        // Create file on spaces
        $file = $request->file('photo');
        $path = $file->store('sub-categories/image', 'spaces');
        // Return success with full url
        return ApiResponse::success('Image uploaded successfully!', ['url' => $path]);
    }

    /**
     * Save project creative brief
     * @param Request $request
     * @return array
     */
    public function editProjectType(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'image' => 'required',
        ]);

        $user = \Auth::user();

        try {
            $result = $this->projectService->editProjectType($request, $user, $id);
            return ApiResponse::success('Project type updated successfully!', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error occurred. Please try again later');
        }
    }

    /**
     * Save project creative brief
     * @param Request $request
     * @return array
     */
    public function uploadFileProjectType(Request $request, $id)
    {
        if (!$request->has('id')) {
            $validatedData = $request->validate(
                [
                    'template' => 'required',
                    'image' => 'required|filled|image|mimes:jpeg,png,jpg,gif,svg',
                ],
            );
        }

        $user = \Auth::user();
        try {
            $result = $this->projectService->uploadFileProjectType($request, $user, $id);
            return ApiResponse::success('Project type updated successfully!', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error occurred. Please try again later');
        }
    }

    /**
     * Save project creative brief
     * @param Request $request
     * @return array
     */
    public function deleteFileProjectType(Request $request, $id)
    {
        try {
            $result = $this->projectService->deleteFileProjectType($id);
            return ApiResponse::success('Project type file deleted successfully!', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error occurred. Please try again later');
        }
    }

    /**
     * Save project creative brief
     * @param Request $request
     * @return array
     */
    public function destroyProjectType(Request $request, $id)
    {
        try {
            $result = $this->projectService->destroyProjectType($id);
            return ApiResponse::success('Project type deleted successfully!', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error occurred. Please try again later');
        }
    }

    /**
     * Save project creative brief
     * @param Request $request
     * @return array
     */
    public function saveCreativeBrief(Request $request)
    {
        $data = $request->all();
        try {
            $this->projectService->saveCreativeBrief($data);
            return ApiResponse::success('Creative brief saved successfully', []);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error occurred. Please try again later');
        }
    }

    public function getCreativeBrief($id)
    {
        try {
            $creativeBrief = $this->projectService->getCreativeBrief($id);
            return ApiResponse::success('Success', $creativeBrief);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error occurred. Please try again later');
        }
    }

    public function getRevisionVersions($project_id)
    {
        $revisions = $this->projectService->getRevisionVersions($project_id);
        $result = [];
        if ($revisions) {
            foreach ($revisions as $key => $revision) {
                $result[$key]['id'] = $revision->id;
                $result[$key]['status'] = $revision->status;
                $result[$key]['value'] = $revision->version;
                $result[$key]['label'] = 'V' . $revision->version;
            }
        }
        return $result;
    }

    public function loadInitialRevision($project_id, $revision_id)
    {
        try {
            $result = $this->projectService->loadInitialRevision($project_id, $revision_id);
            if ($result) {
                return ApiResponse::success('', $result);
            } else {
                return ApiResponse::error('001', 'Error loading revision');
            }
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error loading revision');
        }
    }

    /**
     * Send new revision
     * @param $project_id
     * @param $user_type
     * @return array
     */
    public function sendProject($project_id, $user_type)
    {
        if ($project_id > 0 && $user_type != '') {
            try {
                $result = $this->projectService->sendProject($project_id, $user_type);
                if ($result) {
                    return ApiResponse::success('Project sent successfully', [$result]);
                } else {
                    return ApiResponse::error('001', 'There has been a problem sending the project. Try again in few moments');
                }
            } catch (\Exception $e) {
                report($e);
                return ApiResponse::error('001', 'There has been a problem sending the project. Try again in few moments');
            }
        }

        return ApiResponse::error('001', 'There has been a problem sending the project. Try again in few moments');
    }

    public function loadProofer($project_hash, $revision_id, $comment_id = null)
    {
        if ($project_hash) {
            if (!Auth::check()) {
                Auth::logout();
                return redirect('/login');
            }
            $project = Project::where('project_hash', $project_hash)->first();
            if ($project) {
                $current_revision = $this->revisionService->getRevisionById($revision_id);
                return redirect('/proofer/' . $project->id . '/' . $current_revision->id);
            }

            return redirect('/')->with([
                'message' => json_encode([
                    'title' => 'Thanks for using Prooflo',
                    'body' => 'This project has been deleted and can not be accessed anymore.',
                    'project_data' => Revision::findOrFail($revision_id)->project
                ]),
            ]);
        }
        return view('404');
    }

    public function loadProofer2($project_hash, $revision_id, $proof_id, $issue_id, $comment_id = null)
    {
        if ($project_hash) {
            if (!Auth::check()) {
                Auth::logout();
                return redirect('/login');
            }

            $project = Project::where('project_hash', $project_hash)->first();
            if ($project) {
                $current_revision = $this->revisionService->getRevisionById($revision_id);
                return redirect('/proofer/' . $project->id . '/' . $current_revision->id . '/' . $proof_id . '/' . $issue_id);
            }
        }
        return view('404');
    }

    public function loadProofer3($project_hash, $revision_id, $proof_id)
    {
        if ($project_hash) {
            if (!Auth::check()) {
                Auth::logout();
                return redirect('/login');
            }

            $project = Project::where('project_hash', $project_hash)->first();
            if ($project) {
                $current_revision = $this->revisionService->getRevisionById($revision_id);
                return redirect('/proofer/' . $project->id . '/' . $current_revision->id . '/' . $proof_id);
            }
        }
        return view('404');
    }

    /*  public function loadEditorClient($project_hash, $revision_id)
     {
         if ($project_hash) {
             $project = Project::where('project_hash', $project_hash)->first();
             if ($project) {
                 $current_revision = $this->revisionService->getRevisionById($revision_id);
                 return redirect('/proofer_guest/' . $project->id . '/' . $current_revision->id . '/' . $project_hash);
             }
         }
         return view('404');
     }

     public function loadEditorFreelancer($project_id, $revision_id)
     {
         if ($project_id) {
             if (\Auth::check()) {
                 $project = Project::findOrFail($project_id);
                 if ($project) {
                     $user_id = $project->user_id;
                     if (\Auth::user()->id == $user_id) {
                         $current_revision = $this->revisionService->getRevisionById($revision_id);
                         return redirect('/proofer_freelancer/' . $project_id . '/' . $current_revision->id);
                     }
                 }
             } else {
                 return redirect('/login');
             }
         }
         return view('404');
     } */

    public function approveProject(Request $request)
    {
        $validatedData = $request->validate([
            'project_id' => 'required|integer',
            'revision_id' => 'required|integer',
            'decision' => 'required'
        ]);
        $data = $request->all();

        try {
            return $this->projectService->approveProject($data);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'There has been a problem approving the project. Try again in few moments');
        }
    }

    public function getCurrentUserRol($project_id)
    {
        if ($project_id > 0) {
            $project = Project::findOrFail($project_id);
            if ($project) {
                if ($project->client_id == \Auth::user()->id) {
                    return ApiResponse::success('user rol', 'client');
                }
                if ($project->user_id == \Auth::user()->id) {
                    return ApiResponse::success('user rol', 'freelancer');
                }
            }
            return ApiResponse::error('001', 'Error getting user rol in this project');
        }
    }

    /**
     * Submit Corrections
     * @param $project_id
     * @return array
     */
    public function submitCorrections($project_id)
    {
        try {
            $result = $this->projectService->submitCorrections($project_id);
            if ($result) {
                return ApiResponse::success('Revision sent successfully', $result);
            } else {
                return ApiResponse::error('001', 'There has been a problem sending the revision email back. Try again in few moments');
            }
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'There has been a problem sending the revision email back. Try again in few moments');
        }
    }

    /**
     * Delete project
     * @param $project_id
     * @return array
     */
    public function deleteProject($project_id)
    {
        if ($project_id > 0) {
            $result = $this->projectService->deleteProject($project_id);
            if ($result == true) {
                return ApiResponse::success('Project successfully deleted', []);
            }
        }
        return ApiResponse::error('001', 'There has been a problem deleting the project. Try again in few moments');
    }

    /**
     * Delete project
     * @param $project_id
     * @return array
     */
    public function bulkDeleteProject(Request $request)
    {
        if ($request->selected) {
            $result = $this->projectService->bulkDeleteProject($request);
            return ApiResponse::success('Project successfully deleted', $result);
        }
        return ApiResponse::error('001', 'There has been a problem deleting the project. Try again in few moments');
    }

    /**
     * Get project final files
     * @param $projectId
     * @return array
     */
    public function getFinalFiles($projectId)
    {
        try {
            $result = $this->projectService->getFinalFiles($projectId);
            return ApiResponse::success('Success', $result);
        } catch (\Exception $e) {
            return ApiResponse::error('001', 'Error getting project final files');
        }
    }

    /**
     * Saving and sending final files links
     * @param Request $request
     * @return array
     */
    public function sendFinalFiles(Request $request)
    {
        try {
            $result = $this->projectService->sendfinalFiles($request->all());
            return ApiResponse::success('Final files has been sent by email successfully', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'There has been a problem sending final files. Try again in few moments');
        }
    }

    /**
     * Get single project details
     * @param $projectId
     * @return array
     */
    public function getDetails($projectId)
    {
        if ($projectId) {
            try {
                $details = $this->projectService->getDetails($projectId);
                if ($details) {
                    return ApiResponse::success('', $details);
                } else {
                    return ApiResponse::error('001', 'There has been a problem with this project, please try again later');
                }
            } catch (\Exeption $e) {
                report($e);
                return ApiResponse::error('001', 'There has been a problem with this project, please try again later');
            }
        }
    }

    /**
     * Update project details
     * @param $projectId
     * @param ProjectRequest $request
     * @return array
     */
    public function updateProject($projectId, ProjectRequest $request)
    {
        $data = $request->except('client_name', 'email');
        try {
            $result = $this->projectService->updateProject($projectId, $data);
            return ApiResponse::success('Project updated successfully', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'There has been a problem during update the project, please try again later');
        }
    }

    /**
     * Update project details budget
     * @param $projectId
     * @param ProjectRequest $request
     * @return array
     */
    public function updateProjectBudget($projectId, UpdateBudgetProjectRequest $request)
    {
        try {
            $result = $this->projectService->updateProjectBudget($projectId, $request);
            return ApiResponse::success('Project updated successfully', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'There has been a problem during update the project, please try again later');
        }
    }

    /**
     * Update project details Description
     * @param $projectId
     * @param ProjectRequest $request
     * @return array
     */
    public function updateProjectDescription($projectId, Request $request)
    {
        try {
            $result = $this->projectService->updateProjectDescription($projectId, $request);
            return ApiResponse::success('Project updated successfully', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'There has been a problem during update the project, please try again later');
        }
    }

    /**
     * Update project details Description
     * @param $projectId
     * @param ProjectRequest $request
     * @return array
     */
    public function submitProjectSummary($projectId, Request $request)
    {
        try {
            $result = $this->projectService->submitProjectSummary($projectId, $request);
            return ApiResponse::success('Project updated successfully', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'There has been a problem during update the project, please try again later');
        }
    }

    /**
     * Project invitation clients
     * @param $projectId
     * @param ProjectRequest $request
     * @return array
     */
    public function invitationEmail($projectId, Request $request)
    {
        try {
            $result = $this->projectService->invitationEmail($projectId, $request);
            return ApiResponse::success('Invited client to project successfully', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'There has been a problem during update the project, please try again later');
        }
    }

    /**
     * Project delete invitation collobrator
     * @param $projectId
     * @param ProjectRequest $request
     * @return array
     */
    public function deleteInvitation($projectId, $invitationId)
    {
        try {
            $result = $this->projectService->deleteInvitation($projectId, $invitationId);
            return ApiResponse::success('Invitation deleted successfully', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'There has been a problem during update the project, please try again later');
        }
    }

    /**
     * Project invitation clients
     * @param $token
     * @param ProjectRequest $request
     * @return array
     */
    public function invitationToken($token)
    {
        try {
            $result = $this->projectService->invitationToken($token);
            return ApiResponse::success('Token is correct', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'There has been a problem during update the project, please try again later');
        }
    }

    /**
     * Project invitation clients
     * @param $token
     * @param ProjectRequest $request
     * @return array
     */
    public function fetchClients(Request $request)
    {
        try {
            $result = $this->projectService->fetchClients($request);
            return ApiResponse::success('You got all clients in the system', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'There has been a problem during update the project, please try again later');
        }
    }

    /**
     * Project invitation freelancer
     * @param $token
     * @param ProjectRequest $request
     * @return array
     */
    public function fetchFreelancer(Request $request)
    {
        try {
            $result = $this->projectService->fetchFreelancer($request);
            return ApiResponse::success('You got all freelancers in the system', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'There has been a problem during update the project, please try again later');
        }
    }

    /**
     * Project invitation accept client
     * @param $token
     * @param ProjectRequest $request
     * @return array
     */
    public function invitationAccept($token)
    {
        try {
            $result = $this->projectService->invitationAccept($token);
            return ApiResponse::success('Token has been accepted!', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'There has been a problem during update the project, please try again later');
        }
    }

    /**
     * Project invitation accept client
     * @param $token
     * @param ProjectRequest $request
     * @return array
     */
    public function invitationDecline($token)
    {
        try {
            $result = $this->projectService->invitationDecline($token);
            return ApiResponse::success('Token has been deleted!', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'There has been a problem during update the project, please try again later');
        }
    }

    /**
     * Update project details
     * @param $projectId
     * @param ProjectRequest $request
     * @return array
     */
    public function updateRate($projectId, Request $request)
    {
        $data = $request->all();
        try {
            $result = $this->projectService->updateRate($projectId, $data);
            return ApiResponse::success('Project updated successfully', [$result]);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'There has been a problem during update the project, please try again later');
        }
    }

    /**
     * Move project to InProgress
     * @param $projectId
     * @param $status
     * @return array
     */
    public function changeStatus($projectId, $status)
    {
        try {
            $project = $this->projectService->changeStatus($projectId, $status);
            return ApiResponse::success('Project successfully moved to In Progress', [$project]);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'There has been a problem, please try again later');
        }
    }

    /**
     * Invite new Collaborators
     * @param Request $request
     * @return array
     */
    public function inviteCollaborators(Request $request)
    {
        try {
            $details = $this->projectService->getDetails($request->project_id);
            $project = $details['project'];
            $collaborators = $request->collaborators;

            $result = $this->projectService->registerCollaborators($project, $collaborators);

            return ApiResponse::success('Your invite sent successfully', [$result]);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'There has been a problem sending invite. Try again in few moments');
        }
    }

    /**
     * Add team member to project
     * @param Request $request
     * @return array
     */
    public function addTeamMember(Request $request)
    {
        // try {
        $result  = $this->projectService->addTeamMember($request->all());
        return ApiResponse::success('User added to project successfully', $result);
        // } catch (\Exception $e) {
        report($e);
        return ApiResponse::error('001', 'Something went wrong, please try again later');
        // }
    }

    /**
     * Remove team member from project
     * @param $projectId
     * @param $memberId
     * @return array
     */
    public function deleteTeamMember($projectId, $memberId)
    {
        try {
            $result = $this->projectService->deleteTeamMember($projectId, $memberId);
            if (gettype($result) == 'string') {
                return ApiResponse::error('001', $result);
            }
            return ApiResponse::success('User deleted from project successfully');
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Something went wrong, please try again later');
        }
    }

    /**
     * Notify collobrators and clients that assign to this project
     * @param $projectId
     * @return array
     */
    public function poke(Request $request) {
        try {
            $result = $this->projectService->pokeNotify($request->id);
            return ApiResponse::success('Poke have been sent successfully', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Something went wrong, please try again later');
        }
    }
}
