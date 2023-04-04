<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class SlackNotification extends Notification
{
    use Queueable;

    public $projectName, $webhook, $userID, $content, $icon, $title, $body, $issueLink, $mentionTo;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($projectName, $webhook, $userID, $content, $icon, $title, $body, $issueLink, $mentionTo)
    {
        $this->projectName = $projectName;
        $this->webhook = $webhook;
        $this->userID = $userID;
        $this->content = $content;
        $this->icon = $icon;
        $this->title = $title;
        $this->body = $body;
        $this->issueLink = $issueLink;
        $this->mentionTo = $mentionTo;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        $slack = json_decode(auth()->user()->slack);
        return (new SlackMessage)->content($this->mentionTo . $this->content . "<". $this->issueLink . "|" . $this->projectName . ">\n>" . $this->title . "\n>" . $this->body);
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
