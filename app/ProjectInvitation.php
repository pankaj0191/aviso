<?php

namespace App;

use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Model;

class ProjectInvitation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['project_id', 'user_id', 'email', 'token', 'role', 'permission', 'expire_at'];

    protected $appends = ['link'];

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

    /**
     * Determine if the user is an administrator.
     *
     * @return bool
     */
    public function getLinkAttribute()
    {
        return URL::to('dashboard/invitation/' . $this->attributes['token']);
    }
}
