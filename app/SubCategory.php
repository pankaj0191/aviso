<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'image',
        'width',
        'height',
        'dpi',
        'profile_id',
        'slug',
        'active',
        'size_type',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'sub_category_project', 'sub_category_id', 'project_id');
    }

    public function files()
    {
        return $this->hasMany(CategoryFile::class);
    }
}
