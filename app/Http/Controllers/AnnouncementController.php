<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Repositories\NotificationRepository;
use App\Repositories\AnnouncementRepository;


class AnnouncementController extends Controller
{
    /**
     * The announcements repository.
     *
     * @var AnnouncementRepository
     */
    protected $announcements;


    /**
     * Create a new controller instance.
     *
     * @param  AnnouncementRepository $announcements
     * @param  NotificationRepository $notifications
     * @return void
     */
    public function __construct(AnnouncementRepository $announcements,
                                NotificationRepository $notifications)
    {
        $this->announcements = $announcements;
        $this->notifications = $notifications;

        $this->middleware('auth');
    }


    public function get()
    {

        $announcements = $this->announcements->get();
        return response()->json([
            'announcement' => $announcements
        ]);
    }

    public function delete(Request $request, $id)
    {
        return $announcements = $this->announcements->delete($id);
    }
}
