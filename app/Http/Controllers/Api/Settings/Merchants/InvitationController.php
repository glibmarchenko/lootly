<?php

namespace App\Http\Controllers\Api\Settings\Merchants;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Merchant\CreateInvitationRequest;
use App\Http\Requests\Api\Merchant\UpdateInvitationRequest;
use App\Http\Requests\Api\Merchant\DeleteInvitationRequest;
use App\Mail\MerchantEmployeeInvite;
use App\Merchant;
use App\Models\Invitation;
use App\Repositories\MerchantRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Spark\Contracts\Interactions\Settings\Teams\AddTeamMember;
use Laravel\Spark\Events\Teams\UserInvitedToTeam;
use Ramsey\Uuid\Uuid;

class InvitationController extends Controller
{
    protected $merchantModel;

    public function __construct(MerchantRepository $merchantRepository)
    {
        $this->merchantModel = $merchantRepository;
    }

    public function all(Request $request, Merchant $merchant)
    {
        abort_unless($request->user()->onTeam($merchant), 404);

        return $merchant->invitations;
    }

    public function store(CreateInvitationRequest $request, Merchant $merchant)
    {
        $email = $request->get('email');
        $name = $request->get('name');
        $role = $request->get('role');

        $invitedUser = \Spark::user()->where('email', $email)->first();

        $role = array_key_exists($role, \Spark::roles()) ? $role : \Spark::defaultRole();

        $invitation = $this->createInvitation($merchant, $email, $name, $invitedUser, $role);

        $this->emailInvitation($invitation);

        if ($invitedUser) {
            event(new UserInvitedToTeam($merchant, $invitedUser));
        }

        return $invitation;
    }

    public function update(UpdateInvitationRequest $request, Merchant $merchant)
    {
        $id = $request->get('id');
        $invitation_id = $request->get('invitation_id');
        $email = $request->get('email');
        $name = $request->get('name');
        $role = $request->get('role');
        $status = $request->get('status') ?: 'pending';

        $role = array_key_exists($role, \Spark::roles()) ? $role : \Spark::defaultRole();

        if ($status == 'accepted' && $id) {
            $invitedUser = $merchant->users()->where('user_id', $id)->first();
            if ($invitedUser) {
                $merchant->users()->updateExistingPivot($invitedUser->id, [
                    'invited_by_email' => $email,
                    'invited_by_name'  => $name,
                    'role'             => $role,
                ]);
            }
        } else {
            if ($invitation_id) {
                $invitation = $merchant->invitations()->where('id', $invitation_id)->first();
                if ($invitation) {
                    $invitation->email = $email;
                    $invitation->name = $name;
                    $invitation->role = $role;
                    $invitation->token = str_random(40);
                    $invitation->save();

                    $this->emailInvitation($invitation);
                }
            }
        }
    }

    public function destroy(Request $request, Invitation $invitation)
    {
        abort_unless($request->user()->ownsTeam($invitation->team), 404);

        if ($invitation->user_id) {
            $invitation->team->users()->delete($invitation->user_id);
        }

        $invitation->delete();
    }

    protected function emailInvitation($invitation)
    {
        /*Mail::send($this->view($invitation), compact('invitation'), function ($m) use ($invitation) {
            $m->to($invitation->email)->subject(__('New Invitation!'));
        });*/
    }

    protected function createInvitation($team, $email, $name, $invitedUser, $role)
    {
        $createdAutomatically = false;
        $password = '';

        if (! $invitedUser) {
            // Prepare new user data
            $new_user_data = [];
            $new_user_data['email'] = $email;
            $fullName = explode(' ', $name);
            if (count($fullName) == 1) {
                $new_user_data['first_name'] = $name;
                $new_user_data['last_name'] = '';
            } else {
                $new_user_data['first_name'] = $fullName[0];
                unset($fullName[0]);
                $new_user_data['last_name'] = implode(' ', $fullName);
            }
            $new_user_data['password'] = str_random(8);
            $new_user_data['plan'] = 0;

            // Store user
            $invitedUser = app('user_service')->createNewUser($new_user_data);
            if (! $invitedUser) {
                return abort(500, 'An error has occurred while attempting to create new user record.');
            }
            $createdAutomatically = true;
            $password = $new_user_data['password'];
        }

        try {
            Mail::to($email)->queue(new MerchantEmployeeInvite($invitedUser, $team, $createdAutomatically, $password));
        } catch (\Exception $e) {
            Log::error('An error occurred while attempting to send email notification to user #'.$invitedUser->id.' on merchant #'.$team->id.' employee invite.'.$e->getMessage());
        }

        $invitation = $team->invitations()->create([
            'id'      => Uuid::uuid4(),
            'name'    => $name,
            'user_id' => $invitedUser ? $invitedUser->id : null,
            'role'    => $role,
            'email'   => $email,
            'token'   => str_random(40),
            'status'  => 'accepted',
        ]);

        \Spark::interact(AddTeamMember::class, [
            $invitation->team,
            $invitedUser,
            $invitation->role,
        ]);

        return $invitation;
    }

    protected function view(Invitation $invitation)
    {
        return $invitation->user_id ? 'emails.settings.merchants.invitations.invitation-to-existing-user' : 'emails.settings.merchants.invitations.invitation-to-new-user';
    }

    public function getAllInvitedUsers(Request $request, Merchant $merchant)
    {
        if (! $request->user()->ownsTeam($merchant) && $request->user()->roleOn($merchant) !== 'owner') {
            return [];
        }

        $invites = $merchant->invitations->map(function ($item) {
            return [
                'id'            => $item->user_id,
                'invitation_id' => $item->id,
                'name'          => $item->name,
                'email'         => $item->email,
                'invited_email' => $item->email,
                'invited_name'  => $item->name,
                'role'          => $item->role,
                'status'        => 'pending',
                'statusText'    => 'Pending invite',
                'invited_at'    => $item->created_at,
            ];
        })->toArray();

        $users = $merchant->users->map(function ($item) {
            return [
                'id'            => $item->id,
                'name'          => $item->name,
                'email'         => $item->email,
                'invited_email' => $item->pivot->invited_by_email ?: $item->email,
                'invited_name'  => $item->pivot->invited_by_name ?: $item->name,
                'role'          => $item->pivot->role,
                'status'        => 'accepted',
                'statusText'    => 'Accepted invite'.(isset($item->pivot->created_at) && $item->pivot->created_at ? ' on '.$item->pivot->created_at->format('m/d/Y') : ''),
                'accepted_at'   => $item->pivot->created_at ?: null,
            ];
        })->toArray();

        $merged = $invites;

        for ($i = 0; $i < count($merged); $i++) {
            for ($j = 0; $j < count($users); $j++) {
                if ($users[$j]['id'] === $merged[$i]['id']) {
                    $invitation_id = $merged[$i]['invitation_id'];
                    $merged[$i] = $users[$j];
                    $merged[$i]['invitation_id'] = $invitation_id;
                    $users[$j]['merged'] = 1;
                    break;
                }
            }
        }

        for ($i = 0; $i < count($users); $i++) {
            if (! isset($users[$i]['merged']) || ! $users[$i]['merged']) {
                $merged[] = $users[$i];
            }
        }

        return array_filter($merged, function ($item) use ($request) {
            return $item['id'] !== $request->user()->id;
        });
    }

    public function removeInvitedUser(DeleteInvitationRequest $request, Merchant $merchant)
    {
        $id = $request->get('id');
        $invitation_id = $request->get('invitation_id');
        $status = $request->get('status') ?: 'pending';

        if ($status == 'accepted' && $id) {
            if ($request->user()->id != $id) {
                $invitedUser = $merchant->users()->detach($id);
            }
        }

        if($invitation_id){
            $invitation = $merchant->invitations()->where('id', $invitation_id)->delete();
        }
    }
}