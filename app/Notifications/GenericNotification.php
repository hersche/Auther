<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class GenericNotification extends Notification
{
    use Queueable;
    public $theData;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($theData)
    {
        $this->theData = $theData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
      if(empty($this->theData["link"])){
        return (new MailMessage)
                    ->line($this->theData["appname"])
                    ->line($this->theData["msg"]);
      }
        return (new MailMessage)
                    ->line($this->theData["appname"])
                    ->action($this->theData["msg"], $this->theData["link"]);
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
            "id" => $this->id,
            "msg" => $this->theData["msg"],
            "appname" => $this->theData["appname"],
            "link" => $this->theData["link"]
        ];
    }
}
