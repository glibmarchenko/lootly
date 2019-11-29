<?php

namespace App\Http\Controllers\Settings\Employee;


use Illuminate\Http\Request;
use Laravel\Spark\Contracts\Interactions\Settings\Teams\AddTeamMember;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\Employee\InviteEmployee;
use App\Repositories\MerchantRepository;
use App\Repositories\UserRepository;
use Postmark\PostmarkClient;


class InviteEmployeeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $userRepository, MerchantRepository $merchantRepository)
    {
        $this->userRepository = $userRepository;
        $this->merchantRepository = $merchantRepository;
        $this->middleware('auth');
    }


    /**
     * @param InviteEmployee $request
     * @return mixed
     */
    public function invite(InviteEmployee $request)
    {

        $invitedUser = $request->all();
        $token = str_random(40);


        $merchantObj = $this->merchantRepository->getCurrent();
        if ($merchantObj->id) {
            $invitation = \App\Models\Invitation::create([

                'merchant_id' => $merchantObj->id,
                'email' => $invitedUser['email'],
                'name' => $invitedUser['name'],
                'access' => $invitedUser['access'],
                'status' => 'Pending Invite',
                'token' => $token,
            ]);
            $this->emailInvitation($invitedUser, $token);
            return $invitation;
        } else {
            return \Response::json([
                'error' => 'Please add Store'
            ], 404);
        }
    }

    /**
     * @param $invitedUser
     * @param $token
     */
    private function emailInvitation($invitedUser, $token)
    {
        $subject = 'Create account';
        $client = app('postmark_api')->setup();
        $signature = env('POSTMARK_SIGNATURE');
        $body = $this->getBody($token);

        try {
            $client->sendEmail(
                $signature,
                trim($invitedUser['email']),
                $subject,
                $body);
        }
        catch (\Exception $e) {

        }

    }

    /**
     * @param $token
     * @return string
     */
    private function getBody($token)
    {
        $body = preg_replace('#^https?://#', '', \URL::to('/')) . " Admin invited you to create a staff account at " .
            preg_replace('#^https?://#', '', \URL::to('/')) . "<br>
            <a href=" . env('APP_URL') . "/register/invait/" . $token . "/>Create account</a>";
        return $body;
    }

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {

        $add_team_member = new \Laravel\Spark\Interactions\Settings\Teams\AddTeamMember();
        $add_team_member->handle();
        $this->interaction(
            $request, AddTeamMember::class,
            [$request->user(), $request->all()]
        );

    }
}
