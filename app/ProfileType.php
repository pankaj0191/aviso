<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfileType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'value',
    ];

    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }
}
