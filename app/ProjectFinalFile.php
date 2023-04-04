<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Project;

class ProjectFinalFile extends Model
{
    protected $fillable = ['project_id', 'path', 'size'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
