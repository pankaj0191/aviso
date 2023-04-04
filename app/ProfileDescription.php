<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfileDescription extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'profile_name',
        'title',
        'body',
        'profile_id',
        'hourly_rate',
        'dark_logo',
        'white_logo',
        'storage_capacity'
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
