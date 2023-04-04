<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instruction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'text',
        'project_id',
        'status '
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
