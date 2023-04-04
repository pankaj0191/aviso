<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfileUser extends Model
{
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'user_id',
        'profile_id',
    ];
}
