<?php

namespace App\Http\Controllers\Api\Settings\Merchants;

use Laravel\Spark\Spark;
use Illuminate\Http\Request;
use Laravel\Spark\Invitation;
use Laravel\Spark\Contracts\Interactions\Settings\Teams\AddTeamMember;

class PendingInvitationController extends \Laravel\Spark\Http\Controllers\Settings\Teams\PendingInvitationController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Accept the given invitations.
     *
     * @param  Request  $request
     * @param  \Laravel\Spark\Invitation  $invitation
     * @return Response
     */
    public function accept(Request $request, Invitation $invitation)
    {
        abort_unless($request->user()->id == $invitation->user_id, 404);

        Spark::interact(AddTeamMember::class, [
            $invitation->team, $request->user(), $invitation->role, $invitation->name, $invitation->email
        ]);

        $invitation->delete();
    }
}
