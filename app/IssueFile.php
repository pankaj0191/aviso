<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IssueFile extends Model
{
    protected $fillable = [
        'issue_id',
        'path',
        'thumb_path',
        'size',
        'user_id',
    ];

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }
}
