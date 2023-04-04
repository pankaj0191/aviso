<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ProjectTimer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'user_id',
        'issue_id',

        // Time properties
        'description',
        'is_locked',

        // Time rate
        'amount',
        'hourly_rate',

        // Time Interval
        'duration',
        'start',
        'end'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public static function store($user, $query)
    {
        try {
            $projectTimer = new ProjectTimer();
            $exisitTimer = $projectTimer->where('project_id', $query->project_id)->whereNull('end')->count();
            if ($exisitTimer == 0) {
                $projectTimer->project_id = $query->project_id;
                $projectTimer->issue_id = $query->issue_id;
                $projectTimer->user_id = $user->id;
                $projectTimer->description = $query->description;
                $projectTimer->hourly_rate = $user->currentProfile->profileDescription->hourly_rate;
                $projectTimer->start = $query->start;
                $projectTimer->duration = $query->duration;
                $projectTimer->end = $query->end;
                $projectTimer->save();
                return $projectTimer;
            } else {
                return 'You already started this project!';
            }
        } catch (\Exception $e) {
            return $e;
        }
    }

    public static function updateTimer($query, $id)
    {
        try {
            $projectTimer = ProjectTimer::findOrFail($id);
            $projectTimer->description = $query->description?:$projectTimer->description;
            if ($query->stop) {
                $projectTimer->end = $query->end;
                $projectTimer->duration = Carbon::parse($projectTimer->start)->diffInSeconds(Carbon::parse($projectTimer->end));
                $projectTimer->amount = Carbon::parse($projectTimer->start)->diffInHours(Carbon::parse($projectTimer->end)) * $projectTimer->hourly_rate;
            }
            $projectTimer->save();
            return $projectTimer;
        } catch (\Exception $e) {
            return null;
        }
    }

}
