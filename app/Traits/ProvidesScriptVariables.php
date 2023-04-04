<?php
namespace App\Traits;

use App\Configuration\CallsInteractions;
use App\Configuration\ManagesModelOptions;
use App\Contracts\InitialFrontendState;
use App\Team;
use App\User;
use Illuminate\Support\Str;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Facades\Auth;
use App\Contracts\TeamRepository;


trait ProvidesScriptVariables
{
    /**
     * The alternative implementation of interaction methods.
     *
     * @var array
     */
    public static $interactions = [];
    /**
     * Get the default JavaScript variables for Spark.
     *
     * @return array
     */
    public static function scriptVariables()
    {
        return [
            'translations' => static::getTranslations() + ['teams.team' => trans('teams.team'), 'teams.member' => trans('teams.member')],
            'braintreeMerchantId' => config('services.braintree.merchant_id'),
            'braintreeToken' => null,
            'cardUpFront' => false,
            'collectsBillingAddress' => false,
            'collectsEuropeanVat' => false,
            'createsAdditionalTeams' => true,
            'csrfToken' => csrf_token(),
            'currencySymbol' => env('CASHIER_CURRENCY'),
            'env' => config('app.env'),
            'roles' => [],
            'state' => static::call(InitialFrontendState::class . '@forUser', [Auth::user()]),
//            'state' => ['currentTeam'=>null,'teams'=>[],'user'=>Auth::user()],
            'stripeKey' => config('services.stripe.key'),
            'teamsPrefix' => 'teams',
            'teamsIdentifiedByPath' => !true,
            'userId' => Auth::id(),
            'usesApi' => true,
            'usesBraintree' => false,
            'usesTeams' => static::usesTeams(),
            'usesStripe' => true,
            'chargesUsersPerSeat' =>false,
            'seatName' =>null,
            'extension_id' =>env('CHROME_EXTENSION_ID'),
            'chargesTeamsPerSeat' => false,
            'teamSeatName' =>null,
            'chargesUsersPerTeam' => false,
            'chargesTeamsPerMember' => false,
        ];
    }

    /**
     * Get the translation keys from file.
     *
     * @return array
     */
    private static function getTranslations()
    {
        $translationFile = resource_path('lang/' . app()->getLocale() . '.json');

        if (!is_readable($translationFile)) {
            $translationFile = resource_path('lang/' . app('translator')->getFallback() . '.json');
        }

        return json_decode(file_get_contents($translationFile), true);
    }

    /**
     * Determine if the application offers support for teams.
     *
     * @return bool
     */
    public static function usesTeams()
    {
        return in_array(CanJoinTeams::class, class_uses_recursive('App\User'));
    }

    public static function call($interaction, array $parameters = [])
    {
        return static::interact($interaction, $parameters);
    }

    /**
     * Run an interaction method.
     *
     * @param  string  $interaction
     * @param  array  $parameters
     * @return mixed
     */
    public static function interact($interaction, array $parameters = [])
    {
        if (! Str::contains($interaction, '@')) {
            $interaction = $interaction.'@handle';
        }

        list($class, $method) = explode('@', $interaction);

        if (isset(static::$interactions[$interaction])) {
            return static::callSwappedInteraction($interaction, $parameters, $class);
        }

        $base = class_basename($class);

        if (isset(static::$interactions[$base.'@'.$method])) {
            return static::callSwappedInteraction($base.'@'.$method, $parameters, $class);
        }

//        $data= [
//            'user' => static::currentUser(),
//            'teams' => $parameters ? static::teams($parameters) : [],
//            'currentTeam' => $parameters ? static::currentTeam($parameters) : null,
//        ];
//       return $data;

        return call_user_func_array([app($class), $method], $parameters);
    }

    /**
     * Run a swapped interaction method.
     *
     * @param  string  $interaction
     * @param  array  $parameters
     * @return mixed
     */
    protected static function callSwappedInteraction($interaction, array $parameters, $class)
    {
        if (is_string(static::$interactions[$interaction])) {
            return static::interact(static::$interactions[$interaction], $parameters);
        }

        $instance = app($class);

        $method = static::$interactions[$interaction]->bindTo($instance, $instance);

        return call_user_func_array($method, $parameters);
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    protected static function currentUser()
    {
//        return CallsInteractions::interact(UserRepository::class . '@current');

        if (Auth::check()) {
            $user= User::with(['profiles','projects','unreadComments'])->find(Auth::id())->shouldHaveSelfVisibility();
            $user->load('subscriptions');

            if (static::usesTeams()) {
                $user->load(['ownedTeams.subscriptions', 'teams.subscriptions']);

                $user->currentTeam();
                return  $user;
            }
        }
    }
    /**
     * Get all of the teams for the user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    protected static function teams($user)
    {
//        dd($user);
//        return ManagesModelOptions::usesTeams() ?$user->teams()->with('owner')->get():[];
        return ManagesModelOptions::usesTeams() ? CallsInteractions::interact(
            TeamRepository::class . '@forUser',
            [$user]
        ) : [];
    }

    /**
     * Get the current team for the user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user

     */
    protected static function currentTeam($user)
    {
        if (ManagesModelOptions::usesTeams() && $user->currentTeam()) {
            return Team::with('owner', 'users')->where('id', $user->currentTeam()->id)->first();

        }
    }

}
