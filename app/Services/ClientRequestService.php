<?php

namespace App\Services;

use App\User;
use ZipArchive;
use App\Project;
use App\Category;
use Carbon\Carbon;
use App\Instruction;
use App\ProfileType;
use App\SubCategory;
use App\CategoryFile;
use App\ProjectAssets;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Events\ProjectCreated;
use App\Contracts\IProjectService;
use Illuminate\Support\Facades\DB;
use App\Contracts\IRevisionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Contracts\IClientRequestService;
use MahdiMajidzadeh\LaravelUnsplash\Photo;

class ClientRequestService implements IClientRequestService
{
    private $project;

    protected $disk;
    
    private $projectService;

    private $revisionService;

    public function __construct(IProjectService $projectService, Project $project, IRevisionService $revisionService)
    {
        $this->project = $project;
        $this->projectService = $projectService;
        $this->revisionService = $revisionService;
        $this->disk = Storage::disk(config('filesystems.default'));
    }

    /**
     * @param $user
     * @return mixed
     */
    public function getFreelancersByUser($user)
    {
        $freelancers = [];

        $userProject = User::where('id', Auth::user()->id)->with('projects')->first();
        foreach ($userProject->projects as $key => $project) {
            $freelancer = $project->users()->where('user_id', $project->created_by)->first();

            if (isSubscribed($freelancer) && $freelancer->email != Auth::user()->email) {
                $freelancers[$key] = [
                    'id' => $freelancer->id,
                    'name' => $freelancer->name,
                    'email' => $freelancer->email,
                    'photo_url' => $freelancer->photo_url,
                    'company' => Project::where('created_by', $freelancer->id)->first()->company,
                ];
            }
        }
        return array_unique($freelancers, SORT_REGULAR);
    }

    /**
     * @param $user
     * @return mixed
     */
    public function getWrokers($user)
    {
        $workers = [];
        $num = 0;
        $userProject = User::where('id', Auth::user()->id)->with('projects')->first();
        foreach ($userProject->projects as $key => $project) {
            $worker = $project->users()->where('user_id', $project->created_by)->first();
            $profile = $worker->profiles->where('profile_type_id', ProfileType::where('value', $worker->pivot->role)->first()->id)->first();
            $columns = array_column($workers, 'email');
            if (isSubscribed($worker) && $worker->email != Auth::user()->email && !in_array($worker->email, $columns) && ($worker->pivot->role == 'freelancer' || $worker->pivot->role == 'agency')) {
                $workers[$num] = [
                    'id' => $worker->id,
                    'name' => $profile->name,
                    'email' => $worker->email,
                    'profile' => $profile->id,
                    'photo_url' => $worker->photo_url,
                    'role' => $worker->pivot->role
                ];
                $num++;
            }
        }
        return array_unique($workers, SORT_REGULAR);
    }

    public function getFiles($projectID)
    {
        $project = $this->project->where('id', $projectID)->first()->projectAssets;
        return $project;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function createProject(array $data)
    {
        $clientData['client_name'] = $data['client_name'];
        $clientData['email'] = $data['email'];
        $client = $this->projectService->registerUser($clientData);
        if ($client) {
            //Create project
            $project = $this->project->create([
                'name' => $data['name'],
                'project_hash' => Str::random(40),
                'video_url' => array_key_exists('video_url', $data) ? $data['video_url'] : '',
                'type' => array_key_exists('type', $data) ? $data['type'] : 'design',
                'website_url' => array_key_exists('website_url', $data) ? $data['website_url'] : '',
                'status' => 'draft',
                'company' => $data['company'],
                'job_number' => hexdec(substr(uniqid(), 0, 7)),
                'created_by' => Auth::user()->id,
                'active_stepper' => isset($data['jump']) && $data['jump'] ? 4 : 1
            ]);

            //Create project revision
            $project->revisions()->create([
                'version' => 1,
                'status_revision' => 'draft',
            ]);

            //Create project users
            $project->users()->attach([
                ['user_id' => Auth::user()->id, 'role' => 'client'],
                ['user_id' => $client['user']->id, 'role' => 'freelancer']
            ]);

            //Create project collaborators
            if (array_key_exists('collaborators', $data) && count($data['collaborators'])) {
                $this->projectService->registerCollaborators($project, $data['collaborators']);
            }

            $user = $client['user'];


            //Send new project email if client is already registered
            $revision = $this->revisionService->getLastRevision($project->id);

            event(new ProjectCreated($user, $project, $revision));

            return [
                'newClient' => $client['newUser'],
                'client' => $client['user'],
                'project' => $project
            ];
        }
        return null;
    }

    public function fetchRequest($data)
    {
        $categories = Category::with('subCategories')->get();
        $project = $this->project->with('instructions', 'subCategories.category', 'projectAssets', 'projectPhotos', 'userRoleFreelancer')->where('id', $data['project_id'])->first();
        $userRole = $project->users()->where('user_id', Auth::user()->id)->pluck('role');
        if ($userRole[0] == 'freelancer') {
            return [
                'role' => $userRole[0]
            ];
        }

        $freelancers = [];

        $userProject = User::where('id', Auth::user()->id)->with('projects')->first();
        foreach ($userProject->projects as $key => $projectItem) {
            $freelancer = $projectItem->users()->where('user_id', $projectItem->created_by)->first();
            if (isSubscribed($freelancer)) {
                $freelancers[$key] = [
                    'id' => $freelancer->id,
                    'name' => $freelancer->name,
                    'email' => $freelancer->email,
                    'photo_url' => $freelancer->photo_url,
                    'company' => Project::where('created_by', $freelancer->id)->first()->company,
                ];
            }
        }

        return [
            'categories' => $categories,
            'project' => $project,
            'freelancers' => array_unique($freelancers, SORT_REGULAR),
        ];
    }

    public function createInstruction($data)
    {
        $instruction = [];
        if (isset($data['update']) && $data['update']) {
            Instruction::where('project_id', $data['project_id'])->update([
                'status' => 0
            ]);
            foreach ($data['instructions'] as $instruction) {
                $instruction =  Instruction::where('project_id', $data['project_id'])->where('id', $instruction)->update([
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

    public function createDimension($data)
    {
        $project = $this->project->with('instructions', 'subCategories.category')->where('id', $data['project_id'])->first();
        if ($data->has('active_card')) {
            DB::table('sub_category_project')->where('project_id', $data['project_id'])->delete();
            foreach ($data['active_card'] as $key => $card) {
                $subCat = SubCategory::with('files')->find($card);

                $image = 'projects/' . $project->company . '/' . Carbon::now()->year . '/' . $project->name  . '-' . $project->id . '/' . $project->name . 'Template' . ($key + 1) . '.zip';

                if ($this->disk->exists($image)) {
                    $this->disk->delete($image);
                }
                foreach ($subCat->files as $key => $file) {
                    if ($key == 0) {
                        $this->disk->copy($file->file, $image);
                    }
                }
                $project->subCategories()->attach([
                    ['project_id' => $project->id, 'sub_category_id' => $card, 'copy_path' => ($subCat->files->count() > 0 ? $image : null)],
                ]);
            }
            if (!$data->has('status')) {
                $project->update([
                    'active_stepper' => 2,
                ]);
            }
        } else {
            if ($data->has('sub_categories')) {
                DB::table('sub_category_project')->where('project_id', $data['project_id'])->delete();
                foreach ($data['sub_categories'] as $key => $cat) {
                    $subCat = SubCategory::with('files')->find($cat);
                    $image = 'projects/' . $project->company . '/' . Carbon::now()->year . '/' . $project->name  . '-' . $project->id . '/' . $project->name . 'Template' . ($key + 1) . '.zip';
                    if ($this->disk->exists($image)) {
                        $this->disk->delete($image);
                    }
                    foreach ($subCat->files as $key => $file) {
                        if ($key == 0) {
                            $this->disk->copy($file->file, $image);
                        }
                    }
                    $project->subCategories()->attach([
                        ['project_id' => $project->id, 'sub_category_id' => $cat, 'copy_path' => ($subCat->files->count() > 0 ? $image : null)],
                    ]);
                }
            }
        }

        if ($data->has('customCard') && $data['customCard'] == true) {
            $project->update([
                'width' => $data['width'],
                'height' => $data['height'],
            ]);
        } else {
            $project->update([
                'width' => 0,
                'height' => 0,
            ]);
        }

        return [
            'project' => $project,
        ];
    }

    public function createDescription($data)
    {
        $project = $this->project->with('instructions', 'subCategories.category')
            ->where('id', $data['project_id'])->first();

        if ($data->has('instructions', 'project_id')) {
            $this->createInstruction($data);
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

            $projectAssets = ProjectAssets::where('project_id', $data['project_id'])->where('type', 'photos')->get();
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
                    'project_id' => $data['project_id'],
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


        return [
            'project' => $project,
        ];
    }

    public function createSummary($data)
    {

        if ($data->has('project_id')) {
            $project = $this->project->where('id', $data['project_id'])->first();
        }

        // Client Name
        if ($data->has(['client_name', 'email', 'name', 'company'])) {
            $this->updateName($data);
        }

        if ($data->has('instructions', 'project_id')) {
            $this->createInstruction($data);
        }

        $this->createDimension($data);

        if ($data->has('creative_brief')) {
            $project->update([
                'creative_brief' => $data['creative_brief'],
            ]);
        }
        if ($data->has(['assets_text', 'photos'])) {
            $project->update([
                'assets_text' => $data['assets_text'],
            ]);

            ProjectAssets::where('project_id', $data['project_id'])->where('type', 'photos')->delete();

            foreach ($data['photos'] as $photo) {
                // return dd($photo['links']['download']);
                ProjectAssets::create([
                    'project_id' => $data['project_id'],
                    'url' => json_encode($photo),
                    'type' => 'photos'
                ]);
            }
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

        if ($data->has('status')) {
            $project->update([
                'status' => $data['status'],
            ]);
        } else {
            $project->update([
                'status' => 'progress',
            ]);
        }

        return [
            'project' => $project,
        ];
    }

    public function updateName($data)
    {
        $clientData['client_name'] = $data['client_name'];
        $clientData['email'] = $data['email'];
        $client = $this->projectService->registerUser($clientData);
        $project = $this->project->where('id', $data['project_id'])->first();

        $project->update([
            'name' => $data['name'],
            'company' => $data['company'],
        ]);

        DB::table('project_user')->where('project_id', $data['project_id'])->delete();
        //Create project users
        $project->users()->attach([
            ['user_id' => Auth::user()->id, 'role' => 'client'],
            ['user_id' => $client['user']->id, 'role' => 'freelancer']
        ]);

        return [
            'project' => $project,
        ];
    }
    public function draft($data)
    {
        return $this->createSummary($data);;
    }
}
