<?php

namespace App\Http\Controllers\Kiosk;


class AnnouncementController extends \Laravel\Spark\Http\Controllers\Kiosk\AnnouncementController
{
    /**
     * The announcements repository.
     *
     * @param  \Laravel\Spark\Contracts\Repositories\AnnouncementRepository
     */
    protected $announcements;

    /**
     * Create a new controller instance.
     *
     * @param  \Laravel\Spark\Contracts\Repositories\AnnouncementRepository  $announcements
     * @return void
     */
    public function __construct(\App\Repositories\AnnouncementRepository $announcements)
    {
        parent::__construct($announcements);
        $this->announcements = $announcements;

        $this->middleware('auth');
//        $this->middleware('dev');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function get()
    {

        $announcements = $this->announcements->get();
        return response()->json([
            'announcement' => $announcements
        ]);
    }
}
