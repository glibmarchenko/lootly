<?php

namespace App\SparkExtensions\Interactions\Auth;

use Laravel\Spark\Spark;
use Laravel\Spark\TeamPlan;
use Illuminate\Support\Facades\DB;
use Laravel\Spark\Contracts\Interactions\Subscribe;
use Laravel\Spark\Contracts\Interactions\SubscribeTeam;
use Laravel\Spark\Contracts\Http\Requests\Auth\RegisterRequest;
use Laravel\Spark\Contracts\Interactions\Settings\Teams\CreateTeam;
use Laravel\Spark\Contracts\Interactions\Auth\Register as Contract;
use Laravel\Spark\Contracts\Interactions\Settings\Teams\AddTeamMember;
use Laravel\Spark\Contracts\Interactions\Auth\CreateUser as CreateUserContract;

class Register extends \Laravel\Spark\Interactions\Auth\Register
{
    /**
     * The team created at registration.
     *
     * @var \Laravel\Spark\Team
     */
    private static $team;

    /**
     * Attach the user to a team if an invitation exists, or create a new team.
     *
     * @param  RegisterRequest  $request
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function configureTeamForNewUser(RegisterRequest $request, $user)
    {
        if ($invitation = $request->invitation()) {
            Spark::interact(AddTeamMember::class, [$invitation->team, $user, $invitation->role, $invitation->name, $invitation->email]);

            self::$team = $invitation->team;

            $invitation->delete();
        } elseif (Spark::onlyTeamPlans()) {
            self::$team = Spark::interact(CreateTeam::class, [
                $user, ['name' => $request->team, 'slug' => $request->team_slug]
            ]);

            self::$team->detail()->updateOrCreate([
                'merchant_id' => self::$team->id
            ],[
                'api_key' => str_random(60),
                'api_secret' => str_random(60)
            ]);
        }

        $user->currentTeam();
    }
}
