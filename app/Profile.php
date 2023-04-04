<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'profile_type_id',
        'name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profileType() {
        return $this->belongsTo(ProfileType::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withPivot('active');
    }

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }
    
    /**
     * Profile descriptions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function profileDescription()
    {
        return $this->hasOne(ProfileDescription::class);
    }

    /**
     * Record profile upload filesize
     *
     * @param $file_size
     */
    public function record_upload($file_size)
    {
        $now = Carbon::now();

        $record = Traffic::whereYear('created_at', '=', $now->year)
            ->whereMonth('created_at', '=', $now->month)
            ->firstOrCreate([
                'profile_id' => $this->id,
            ]);

        $record->update([
            'upload' => $record->upload + $file_size
        ]);
    }

    /**
     * Record profile download filesize
     *
     * @param $file_size
     */
    public function record_download($file_size)
    {
        $now = Carbon::now();

        $record = Traffic::whereYear('created_at', '=', $now->year)
            ->whereMonth('created_at', '=', $now->month)
            ->firstOrCreate([
                'user_id' => $this->id,
            ]);

        $record->update([
            'download' => $record->download + $file_size
        ]);
    }
}
