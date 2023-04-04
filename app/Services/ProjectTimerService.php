<?php

namespace App\Services;

use App\ProjectTimer;
use Illuminate\Support\Facades\DB;
use App\Contracts\IProjectTimerService;


class ProjectTimerService implements IProjectTimerService
{
    private $model;

    public function __construct(ProjectTimer $model)
    {
        $this->model = $model;
    }

    public function getProjectTimersByUser($user)
    {
        return $this->model->with('project.userRoleFreelancer','project.userRoleClient', 'project.client.ownedTeams.users')->where('user_id', $user->id)->get();
    }

    public function getOneProjectTimer($id)
    {
        return $this->model->where('id', $id)->firstOrFail();
    }

    public function storeTimer($user, $data)
    {
        try {
            $projectStore = $this->model->store($user, $data);
            if ($projectStore != null) {
                return $projectStore;
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    public function updateTimer($data, $id)
    {
        try {
            $projectStore = $this->model->updateTimer($data, $id);
            if ($projectStore != null) {
                return $projectStore;
            }
        } catch (\Exception $e) {
            return null;
        }
    }
    public function deleteTimer($id)
    {
        try {
            $projectStore = $this->model->destroy($id);
            if ($projectStore != null) {
                return $projectStore;
            }
        } catch (\Exception $e) {
            return null;
        }
    }
}
