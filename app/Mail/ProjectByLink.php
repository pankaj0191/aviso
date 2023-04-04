<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectByLink extends Mailable
{
    use Queueable, SerializesModels;

    use Queueable, SerializesModels;

    public $sent_by_name;
    public $link;
    public $project;
    public $revision;

    /**
     * Create a new message instance.
     * @param $name
     * @param $project
     * @param $revision
     */
    public function __construct($user, $link, $project, $revision)
    {
        $this->sent_by_name = $user->name;
        $this->link = $link;
        $this->project = $project;
        $this->revision = $revision;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Project | Please Login to Review')->view('emails.projects.new_project_by_link');
    }
}
