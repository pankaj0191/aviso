<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Http\Request;
use App\Contracts\INotificationService;
use App\Contracts\AnnouncementRepository;

class NotificationController extends Controller
{
    protected $announcements;
    protected $notifications;

    /**
     * NotificationController constructor.
     * @param AnnouncementRepository $announcements
     * @param INotificationService $notifications
     */
    public function __construct(AnnouncementRepository $announcements, INotificationService $notifications)
    {
        $this->announcements = $announcements;
        $this->notifications = $notifications;

        $this->middleware('auth');
    }

    /**
     * Get the recent notifications and announcements for the user.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recent(Request $request)
    {
        return response()->json([
            'announcements' => $this->announcements->recent()->toArray(),
            'notifications' => $this->notifications->recent($request->user())->toArray(),
        ]);
    }
    /**
     * Get the recent notifications and announcements for the user.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetch(Request $request)
    {
        return response()->json([
            'notifications' => $this->notifications->fetch($request->user()),
        ]);
    }

    /**
     * Mark the given notifications as read.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function markAsReadOne(Request $request)
    {
        Notification::where('id', $request->notifications)->whereUserId($request->user()->id)->update(['read' => 2]);
    }
}