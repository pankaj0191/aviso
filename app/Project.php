<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;


// TODO: remove all unused functions and relations from model
class Project extends Model
{
    use HasEagerLimit;

    protected $fillable = [
        'name',
        'creative_brief',
        'company',
        'status',
        'project_hash',
        'type',
        'website_url',
        'video_url',
        'created_by',
        'job_number',
        'width',
        'height',
        'active_stepper',
        'file_types',
        'assets_text',
        'start',
        'end',
        'rate',
        'review',
        'budget',
    ];

    /**
     * Project Users
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role', 'first_open', 'hourly_rate', 'review_project')->withTimestamps();
    }
    /**
     * Project Users
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function usersHourlyRate()
    {
        return $this->belongsToMany(User::class)->withPivot('role', 'first_open', 'hourly_rate')->wherePivot('hourly_rate', '>', 0)->withTimestamps();
    }

    /**
     * Project Users
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function userRoleClient()
    {
        return $this->belongsToMany(User::class)->withPivot('role', 'first_open')->wherePivot('role', 'client')->withTimestamps();
    }

    /**
     * Project Users
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function userRoleFreelancer()
    {
        return $this->belongsToMany(User::class)->withPivot('role', 'first_open')->wherePivot('role', 'freelancer')->withTimestamps();
    }

    /**
     * Project Users
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function userRoleAgency()
    {
        return $this->belongsToMany(User::class)->withPivot('role', 'first_open')->wherePivot('role', 'agency')->withTimestamps();
    }

    /**
     * Project Client
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Project Client
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Project Revisions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function revisions()
    {
        return $this->hasMany(Revision::class);
    }

    /**
     * Project files final links
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function finalLinks()
    {
        return $this->hasMany(ProjectFinalLink::class);
    }

    /**
     * Project final files
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function finalFiles()
    {
        return $this->hasMany(ProjectFinalFile::class);
    }

    /**
     * Project instruction
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function instructions()
    {
        return $this->hasMany(Instruction::class);
    }

    /**
     * Project Tracker
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projectTimers()
    {
        return $this->hasMany(ProjectTimer::class);
    }

    /**
     * Project unread comments
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function unreadComments()
    {
        return $this->hasMany(UnreadComment::class, 'project_id');
    }

    /**
     * Project unread projectAssets
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projectAssets()
    {
        return $this->hasMany(ProjectAssets::class)->where('type', 'assets');
    }
    public function projectPhotos()
    {
        return $this->hasMany(ProjectAssets::class)->where('type', 'photos');
    }

    public function subCategories()
    {
        return $this->belongsToMany(SubCategory::class, 'sub_category_project', 'project_id', 'sub_category_id');
    }

    /**
     * Is project published
     *
     * @return bool
     */
    public function published()
    {
        return !$this->inDraft();
    }

    /**
     * Is project in drafts
     *
     * @return bool
     */
    public function inDraft()
    {
        return $this->status === 'draft';
    }

    /**
     * Is project progress
     *
     * @return bool
     */
    public function inProgress()
    {
        return $this->status === 'progress';
    }


    // TODO: move this functions to repository class

    public static function getActiveRevision($project_id)
    {
        try {
            $revision = Revision::where('project_id', $project_id)->orderBy('version', 'desc')->firstOrFail();
            return $revision;
        } catch (Illuminate\Database\QueryException $e) {
            return $e->getMessage();
        }
    }

    public static function getActiveTimeTracker($project_id)
    {
        try {
            $projectTimer = ProjectTimer::where('project_id', $project_id)->whereNull('issue_id')->whereNull('end')->first();
            return $projectTimer;
        } catch (Illuminate\Database\QueryException $e) {
            return $e->getMessage();
        }
    }

    public static function getTotalTimeTracker($project_id)
    {
        try {
            $total = 0;
            $projectTimer = ProjectTimer::where('project_id', $project_id)->whereNotNull('end')->get();
            foreach ($projectTimer as $timer) {
                $total += $timer->duration;
            }
            return $total;
        } catch (Illuminate\Database\QueryException $e) {
            return $e->getMessage();
        }
    }


    public function scopeTotalTime($query)
    {
        return $query->withCount(['projectTimers as totalTracker' => function ($q) {
            $q->select(\DB::raw("SUM(duration) as totalTracker"))->whereNotNull('end');
        }]);
    }

    public function scopeFilterIssues($query, $user, $data)
    {

        if ($data->user > 0) {
            return $query->with(['users', 'revisions.proofs' => function ($q) use ($data) {
                $q->with(['issues' => function ($q) use($data) {
                    $q->where('assign_id', $data->user)->totalTime()->with('timeTracker', 'assign');
                }])->whereHas('issues', function ($q) use ($data) {
                    $q->where('assign_id', $data->user);
                });
            }])->whereHas('revisions.proofs.issues', function ($q) use ($data) {
                $q->where('assign_id', $data->user);
            });
        }
        if ($data->task == 'My Tasks') {
            return $query->with(['users', 'revisions.proofs' => function ($q) use ($user) {
                $q->with(['issues' => function ($q) use($user) {
                    $q->where('assign_id', $user->id)->totalTime()->with('timeTracker', 'assign');
                }])->whereHas('issues', function ($q) use ($user) {
                    $q->where('assign_id', $user->id);
                });
            }])->whereHas('revisions.proofs.issues', function ($q) use ($user) {
                $q->where('assign_id', $user->id);
            });
        } 
        return $query->has('revisions.proofs.issues')->with(['users', 'revisions.proofs' => function ($q) {
            $q->with(['issues' => function ($q) {
                $q->totalTime()->with('timeTracker', 'assign');
            }]);
        }]);
    }

    public static function getRevisionVersions($project_id)
    {
        try {
            $project = Project::findOrFail($project_id);
            $revisions = $project->revisions()->orderBy('created_at', 'desc')->get();
            return $revisions;
        } catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $e->getMessage();
        }
    }

    public function projectInvitations()
    {
        return $this->hasMany(ProjectInvitation::class);
    }
}
