<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectAssets extends Model
{
    protected $fillable = ['project_id', 'url', 'thumb_path', 'size', 'type'];

    /**
     * Project unread projectAssets
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
