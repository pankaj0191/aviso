<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Issue extends Model
{
    protected $fillable = ['title', 'user_id', 'proof_id', 'project_files_id', 'group', 'assign_id', 'tag', 'likes', 'start_date', 'end_date'];

    protected $appends = ['user'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'likes' => 'array'
    ];
   
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assign()
    {
        return $this->belongsTo(User::class, 'assign_id');
    }

    public function proof()
    {
        return $this->belongsTo(Proof::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->with('files')->with('user');
    }

    public function files()
    {
        return $this->hasMany(IssueFile::class);
    }

    public function getUserAttribute()
    {
        return $this->user();
    }

    public function unreadComments()
    {
        return $this->hasMany(UnreadComment::class, 'issue_id')->where('user_id', '=', Auth::user()->id);
    }

    public function timeTracker()
    {
        return $this->hasOne(ProjectTimer::class, 'issue_id')->whereNull('end');
    }

    public function scopeTotalTime($query)
    {
         $query->withCount(['projectTimers as totalTracker' => function($q) {
            $q->select(\DB::raw("SUM(duration) as totalTracker"))->whereNotNull('end');
        }]);

        return $query;
    }

    public function projectTimers()
    {
        return $this->hasMany(ProjectTimer::class, 'issue_id');
    }

}
