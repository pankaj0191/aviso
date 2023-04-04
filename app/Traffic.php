<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Traffic extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'profile_id',
        'upload',
        'download',
    ];
}
