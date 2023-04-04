<?php

namespace App;

use Carbon\Carbon;
use App\Traits\Billable;
use App\Spark;
use App\Mail\ResetPassword;
use Illuminate\Support\Str;
use App\Traits\CanJoinTeams;
use App\Traits\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\RoutesNotifications;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use CanJoinTeams;
    use Notifiable;
    use HasRoles;
    use Billable;
    use HasApiTokens;
    use RoutesNotifications;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'verified',
        'activation_code',
        'address',
        'city',
        'phone',
        'company',
        'country_code',
        'freelancer_name',
        'slack'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'authy_id',
        'card_brand',
        'card_last_four',
        'card_country',
        'billing_address',
        'billing_address_line_2',
        'billing_city',
        'billing_zip',
        'billing_country',
        'extra_billing_information',
        'activation_code',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'uses_two_factor_auth' => 'boolean',
    ];

    public function projects()
    {
        return $this->belongsToMany(Project::class)->withPivot('role')->withTimestamps();
    }

    public function projectsFreelancer()
    {
        return $this->belongsToMany(Project::class)->withPivot('role')->withTimestamps();
    }

    public function projectReviews()
    {
        return $this->belongsToMany(Project::class)->withPivot('role', 'hourly_rate', 'review_project')->withTimestamps()->whereNotNull('rate');
    }

    /**
     * Get user email notifications settings
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function emailNotifications()
    {
        return $this->hasOne(EmailNotificationSettings::class);
    }

    /**
     * Get the token that belongs to user.
     */
    public function token()
    {
        return $this->hasOne(APIToken::class);
    }

    public function unreadComments()
    {
        return $this->hasMany(UnreadComment::class, 'user_id');
    }

    /**
     * Get subscribed plan details
     * @return mixed
     */
    public function currentPlan()
    {
        if ($this->current_billing_plan) {
            return Plan::find($this->current_billing_plan);
        }
        return null;
    }

    public function sendPasswordResetNotification($token)
    {
        $email = $this->getEmailForPasswordReset();

        Mail::to($email)->send(new ResetPassword($token));
    }

    public function getRoleOf(Project $project)
    {
        if (!$this->projects->contains($project)) {
            throw new \Exception("User has not any kind of role in '{$project->name}' -project", 1);
        }

        return $project->users
            ->where('id', $this->id)->first()
            ->pivot->role;
    }

    public function isFreelancerOf(Project $project)
    {
        return $this->getRoleOf($project) === 'freelancer';
    }

    public function isClientOf(Project $project)
    {
        return $this->getRoleOf($project) === 'client';
    }

    public function scopeActiveTrial($query)
    {
        return $query->whereDate('trial_ends_at', '>=', Carbon::today());
    }

    /**
     * {@inheritdoc}
     */
    public function scopeYearlyRecurringRevenue($query)
    {
        return $this->monthlyRecurringRevenue() * 12;
    }

    /**
     * Get the monthly recurring revenue.
     *
     * @return float
     */
    public function scopeMonthlyRecurringRevenue($query)
    {
        return $this->recurringRevenueByInterval('monthly') +
            ($this->recurringRevenueByInterval('yearly') / 12);
    }

    /**
     * Get the recurring revenue for the given interval.
     *
     * @param  string  $interval
     * @return float
     */
    protected function recurringRevenueByInterval($interval)
    {
        $total = 0;

        $plans = Plan::all();

        foreach ($plans as $plan) {
            $total += DB::table('subscriptions')
                ->where($this->planColumn(), $plan->stripe_plan_id)
                ->where(function ($query) {
                    $query->whereNull('trial_ends_at')
                        ->orWhere('trial_ends_at', '<=', Carbon::now());
                })
                ->whereNull('ends_at')
                ->sum('quantity') * $plan->price;
        }

        return $total;
    }

    /**
     * Get the plan column name.
     *
     * @return string
     */
    protected function planColumn()
    {
        return 'stripe_plan';
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * User Tracker
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projectTimers()
    {
        return $this->hasMany(ProjectTimer::class);
    }


    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }

    public function projectInvitations()
    {
        return $this->hasMany(ProjectInvitation::class);
    }

    public function currentProfile()
    {
        return $this->belongsTo(Profile::class, 'current_profile_id');
    }

    public function scopeFilterClient($query, $data)
    {
        $query->where('id', '<>', auth()->user()->id)->where('name', 'like', '%' . $data['queryString'] . '%')
            ->orWhere('email', 'like', '%' . $data['queryString'] . '%');

        return $query;
    }

    public function routeNotificationForSlack($notification)
    {
        return $notification->webhook;
    }

    /**
     * Get the profile photo URL attribute.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getPhotoUrlAttribute($value)
    {
        return empty($value) ? 'https://www.gravatar.com/avatar/'.md5(Str::lower($this->email)).'.jpg?s=200&d=mm' : url($value);
    }

    /**
     * Make the team user visible for the current user.
     *
     * @return $this
     */
    public function shouldHaveSelfVisibility()
    {
        return $this->makeVisible([
            'uses_two_factor_auth',
            'country_code',
            'phone',
            'card_brand',
            'card_last_four',
            'card_country',
            'billing_address',
            'billing_address_line_2',
            'billing_city',
            'billing_state',
            'billing_zip',
            'billing_country',
            'extra_billing_information'
        ]);
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();

        if (! in_array('tax_rate', $this->hidden)) {
            $array['tax_rate'] = $this->taxPercentage();
        }

        return $array;
    }
}
