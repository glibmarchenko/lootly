<?php

namespace App\Providers;

use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Spark\Contracts\Http\Requests\Auth\RegisterRequest;
use Laravel\Spark\Contracts\Interactions\Auth\CreateUser;
use Laravel\Spark\Contracts\Interactions\Auth\Register;
use Laravel\Spark\Contracts\Interactions\Settings\Profile\UpdateContactInformation;
use Laravel\Spark\Contracts\Interactions\Settings\Teams\AddTeamMember;
use Laravel\Spark\Contracts\Repositories\UserRepository;
use Laravel\Spark\Events\Profile\ContactInformationUpdated;
use Laravel\Spark\Events\Teams\TeamMemberAdded;
use Laravel\Spark\Repositories\TeamRepository;
use Laravel\Spark\Spark;
use Laravel\Spark\Providers\AppServiceProvider as ServiceProvider;

class SparkServiceProvider extends ServiceProvider
{
    /**
     * Your application and company details.
     *
     * @var array
     */
    protected $details = [
        'vendor'   => 'Your Company',
        'product'  => 'Your Product',
        'street'   => 'PO Box 111',
        'location' => 'Your Town, NY 12345',
        'phone'    => '555-555-5555',
    ];

    /**
     * The address where customer support e-mails should be sent.
     *
     * @var string
     */
    protected $sendSupportEmailsTo = null;

    /**
     * All of the application developer e-mail addresses.
     *
     * @var array
     */
    protected $developers = [
        'user@user.com',
        'larry@trustspot.io'
    ];

    /**
     * Indicates if the application will expose an API.
     *
     * @var bool
     */
    protected $usesApi = true;

    /**
     * Finish configuring Spark for the application.
     *
     * @return void
     */
    public function booted()
    {
        /*
        Spark::useStripe()->noCardUpFront()->trialDays(30);
        Spark::freePlan()
            ->features([
                'First', 'Second', 'Third'
            ]);

        Spark::plan('Basic', 'provider-id-1')
            ->price(10)
            ->features([
                'First', 'Second', 'Third'
            ]);*/

        Spark::useStripe()->noCardUpFront()->teamTrialDays(30);

        /*Spark::freeTeamPlan()->features([
            'First',
            'Second',
            'Third',
        ]);

        Spark::teamPlan('Basic', 'provider-id-1')->price(10)->features([
            'First',
            'Second',
            'Third',
        ]);*/

        // Get plans
        try {
            $plans = Plan::all();
            for ($i = 0; $i < count($plans); $i++) {
                Spark::plan($plans[$i]->name, $plans[$i]->type)->price($plans[$i]->price);
                Spark::teamPlan($plans[$i]->name, $plans[$i]->type)->price($plans[$i]->price);
            }
        }catch(\Exception $e){
           Log::error('Cannot get subscription plans: '.$e->getMessage());
        }

        Spark::useRoles([
            'member' => 'Member',
            'owner'  => 'Owner',
        ]);

        Spark::swap(CreateUser::class.'@validator', function ($request) {

            $rules = [
                'first_name' => 'max:191',
                // required
                'last_name'  => 'max:191',
                // required
                'email'      => 'required|email|max:191|unique:users',
                'password'   => 'required|min:'.Spark::minimumPasswordLength(),
                // confirmed
                'vat_id'     => 'nullable|max:50|vat_id',
                //'terms'      => 'required|accepted',
            ];

            $validator = Validator::make($request->all(), $rules, [], [
                'address_line_2' => __('second address line'),
                'team'           => __('store'),
            ]);

            $validator->sometimes('team', 'required|max:255', function ($input) {
                return Spark::usesTeams() && Spark::onlyTeamPlans() && ! isset($input['invitation']);
            });

            $validator->sometimes('team_slug', 'required|alpha_dash|unique:teams,slug', function ($input) {
                return Spark::usesTeams() && Spark::onlyTeamPlans() && Spark::teamsIdentifiedByPath() && ! isset($input['invitation']);
            });

            return $validator;
        });

        Spark::swap(UserRepository::class.'@create', function (array $data) {
            $user = Spark::user();

            $user->forceFill([
                'first_name'                 => $data['first_name'] ?? null,
                'last_name'                  => $data['last_name'] ?? null,
                'email'                      => $data['email'],
                'password'                   => bcrypt($data['password']),
                'last_read_announcements_at' => Carbon::now(),
                'trial_ends_at'              => Carbon::now()->addDays(Spark::trialDays()),
            ])->save();

            return $user;
        });

        Spark::swap(UserRepository::class.'@search', function ($query, $excludeUser = null) {
            $search = Spark::user()->with('subscriptions');

            // If a user to exclude was passed to the repository, we will exclude their User
            // ID from the list. Typically we don't want to show the current user in the
            // search results and only want to display the other users from the query.
            if ($excludeUser) {
                $search->where('id', '<>', $excludeUser->id);
            }

            return $search->where(function ($search) use ($query) {
                $search->where('email', 'like', '%'.$query.'%');
                $search->orWhere('first_name', 'like', '%'.$query.'%');
                $search->orWhere('last_name', 'like', '%'.$query.'%');
            })->get();
        });

        Spark::swap(UpdateContactInformation::class.'@validator', function ($user, array $data) {
            return Validator::make($data, [
                'first_name'    => 'required|max:191',
                'last_name'     => 'required|max:191',
                'email'         => 'required|email|unique:users,email,'.$user->id,
                'billing_email' => 'required|email',
            ]);
        });

        Spark::swap(UpdateContactInformation::class.'@handle', function ($user, array $data) {
            $user->forceFill([
                'first_name'    => $data['first_name'],
                'last_name'     => $data['last_name'],
                'email'         => $data['email'],
                'billing_email' => $data['billing_email'],
            ])->save();

            event(new ContactInformationUpdated($user));

            return $user;
        });

        Spark::swap(AddTeamMember::class.'@handle', function ($team, $user, $role = null, $name = null, $email = null) {
            $team->users()->attach($user, [
                'role'             => $role ?: Spark::defaultRole(),
                'invited_by_name'  => $name ?: null,
                'invited_by_email' => $email ?: null,
            ]);

            event(new TeamMemberAdded($team, $user));
        });

        Spark::swap(\Laravel\Spark\Contracts\Repositories\TeamRepository::class.'@create', function ($user, array $data) {
            $attributes = array_merge($data, [
                'owner_id'      => $user->id,
                'name'          => $data['name'],
                'trial_ends_at' => Carbon::now()->addDays(Spark::teamTrialDays()),
            ]);

            if (Spark::teamsIdentifiedByPath()) {
                $attributes['slug'] = $data['slug'];
            }

            return Spark::team()->forceCreate($attributes);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Spark::useTeamModel('App\Merchant');

        Spark::prefixTeamsAs('merchants');

        $this->app->singleton(\Laravel\Spark\TeamSubscription::class, \App\SparkExtensions\TeamSubscription::class);

        $this->app->singleton(\Laravel\Spark\Interactions\Auth\Register::class, \App\SparkExtensions\Interactions\Auth\Register::class);

        $this->registerServices();
    }

    /**
     * Register the Spark services.
     *
     * @return void
     */
    protected function registerServices()
    {
        $services = [
            'Contracts\Interactions\Settings\Store\UpdateStoreDetail' => 'Interactions\Settings\Store\UpdateStoreDetail',
        ];

        foreach ($services as $key => $value) {
            $this->app->singleton('App\\'.$key, 'App\\'.$value);
        }
    }
}
