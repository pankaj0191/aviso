<?php

namespace App\Interactions\Auth;

use App\Spark;
use App\TeamPlan;
use Illuminate\Support\Facades\DB;
use App\Contracts\Interactions\Subscribe;
use App\Contracts\Interactions\SubscribeTeam;
use App\Contracts\Http\Requests\Auth\RegisterRequest;
use App\Contracts\Interactions\Settings\Teams\CreateTeam;
use App\Contracts\Interactions\Auth\Register as Contract;
use App\Contracts\Interactions\Settings\Teams\AddTeamMember;
use App\Contracts\Interactions\Auth\CreateUser as CreateUserContract;

class Register implements Contract
{
    /**
     * The team created at registration.
     *
     * @var \App\Team
     */
    private static $team;

    /**
     * {@inheritdoc}
     */
    public function handle(RegisterRequest $request)
    {
        return DB::transaction(function () use ($request) {
            return $this->subscribe($request, $this->createUser($request));
        });
    }

    /**
     * Create the user for the new registration.
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    protected function createUser(RegisterRequest $request)
    {
        $user = Spark::interact(CreateUserContract::class, [$request]);

        if (Spark::usesTeams()) {
            Spark::interact(self::class.'@configureTeamForNewUser', [$request, $user]);
        }

        return $user;
    }

    /**
     * Attach the user to a team if an invitation exists, or create a new team.
     *
     * @param RegisterRequest $request
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function configureTeamForNewUser(RegisterRequest $request, $user)
    {
        if ($invitation = $request->invitation()) {
            Spark::interact(AddTeamMember::class, [$invitation->team, $user, $invitation->role]);

            self::$team = $invitation->team;

            $invitation->delete();
        } elseif (Spark::onlyTeamPlans()) {
            self::$team = Spark::interact(CreateTeam::class, [
                $user, ['name' => $request->team, 'slug' => $request->team_slug]
            ]);
        }

        $user->currentTeam();
    }

    /**
     * Subscribe the given user to a subscription plan.
     *
     * @param  \Laravel\Spark\Contracts\Http\Requests\Auth\RegisterRequest  $request
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    protected function subscribe($request, $user)
    {
        if (! $request->hasPaidPlan()) {
            return $user;
        }

        if ($request->plan() instanceof TeamPlan) {
            Spark::interact(SubscribeTeam::class, [
                self::$team, $request->plan(), true, $request->all()
            ]);
        } else {
            Spark::interact(Subscribe::class, [
                $user, $request->plan(), true, $request->all()
            ]);
        }

        return $user;
    }
}
