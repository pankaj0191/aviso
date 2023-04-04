<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailNotificationSettings extends Model
{
    protected $fillable = [
        'user_id',
        'new_project',
        'new_comment',
        'new_correction',
        'new_revision',
        'approved_project',
        'completed_project',
        'review_project',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
