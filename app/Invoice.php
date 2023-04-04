<?php

namespace App;

use App\Team;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'team_id',
        'provider_id',
        'total',
        'tax',
        'card_country',
        'biling_state',
        'billing_zip',
        'billing_country',
        'vat_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function team()
    {
        return $this->belongsTo(Team::class, 'user_id');
    }
}
