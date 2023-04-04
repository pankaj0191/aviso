<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryFile extends Model
{
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
}
