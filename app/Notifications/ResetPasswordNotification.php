<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;
class ResetPasswordNotification extends Notification
{
    use Queueable;
    public $token = '';
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if(config("auth.guards.web.driver")==="jwt") {
            $jwt_token = Auth::tokenById(Auth::id());
            return (new MailMessage)
                ->line('You wanted to reset your password.')
                ->action('Reset password', url('/password/reset/' . $this->token) . "&token=" . $jwt_token)
                ->line('Thank you for using our application and have fun!');
        }
        return (new MailMessage)
            ->line('You wanted to reset your password.')
            ->action('Reset password', url('/password/reset/' . $this->token))
            ->line('Thank you for using our application and have fun!');
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
