<?php

namespace App\Http\Controllers\Settings\Employee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\InvitationRepository;



class EmployeeSettingsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(InvitationRepository $invitationRepository)
    {
        $this->invitationRepository = $invitationRepository;
        $this->middleware('auth');
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request)
    {
        $data = $request->all();
        $response = $this->invitationRepository->update($data);

        return response()->json([
            'response' => $response
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, $id)
    {
        $data = $request->all();
        $response = $this->invitationRepository->delete($id);

        return response()->json([
            'response' => $response
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filter(Request $request)
    {
        $data = $request->all();
        $result = $this->invitationRepository->filter($data);
        return response()->json([
            'result' => $result
        ]);
    }
}
