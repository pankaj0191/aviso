<?php

namespace App\Observers;

use Ramsey\Uuid\Uuid;
use App\Announcement;
use Illuminate\Support\Facades\Auth;

class AnnouncementObserver
{
    /**
     * Handle the announcement "created" event.
     *
     * @param  \App\Announcement  $announcement
     * @return void
     */
    public function saving(Announcement $announcement)
    {
        $announcement->id = Uuid::uuid4();
        $announcement->user_id = Auth::id();
    }

    /**
     * Handle the announcement "updated" event.
     *
     * @param  \App\Announcement  $announcement
     * @return void
     */
    public function updating(Announcement $announcement)
    {
        //        
    }

    /**
     * Handle the announcement "deleted" event.
     *
     * @param  \App\Announcement  $announcement
     * @return void
     */
    public function deleted(Announcement $announcement)
    {
        //
    }

    /**
     * Handle the announcement "restored" event.
     *
     * @param  \App\Announcement  $announcement
     * @return void
     */
    public function restored(Announcement $announcement)
    {
        //
    }

    /**
     * Handle the announcement "force deleted" event.
     *
     * @param  \App\Announcement  $announcement
     * @return void
     */
    public function forceDeleted(Announcement $announcement)
    {
        //
    }
}
