<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'stripe_plan_id',
        'stripe_product_id',
        'name',
        'description',
        'price',
        'teams_count',
        'teams_members_count',
        'trial_days',
        'interval',
        'quantity',
        'storage_capacity'
    ];

    /**
     * Get the user's first name.
     *
     * @param  string  $value
     * @return string
     */
    public function getPriceAttribute($value)
    {
        return $value / 100;
    }

    /**
     * Set the user's first name.
     *
     * @param  string  $value
     * @return void
     */
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = $value * 100;
    }

}
