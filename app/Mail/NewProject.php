<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewProject extends Mailable
{
    use Queueable, SerializesModels;

    public $sent_by_name;
    public $name;
    public $project;
    public $revision;

    /**
     * Create a new message instance.
     * @param $name
     * @param $project
     * @param $revision
     */
    public function __construct($user, $name, $project, $revision)
    {
        $this->sent_by_name = $user->name;
        $this->name = $name;
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
        return $this->subject('New Project | Please Login to Review')->view('emails.projects.new_project');
    }
}
