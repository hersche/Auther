<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
class VerificateEmailNotification extends Notification
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
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject(\Lang::getFromJson('Verify Email Address'))
                ->line(\Lang::getFromJson('Please click the button below to verify your email address.'))
                ->action(
                    \Lang::getFromJson('Verify Email Address'),
                    $this->verificationUrl($notifiable) . "&token=" . $jwt_token
                )
                ->line(\Lang::getFromJson('If you did not create an account, no further action is required.'));
        }
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject(\Lang::getFromJson('Verify Email Address'))
            ->line(\Lang::getFromJson('Please click the button below to verify your email address.'))
            ->action(
                \Lang::getFromJson('Verify Email Address'),
                $this->verificationUrl($notifiable)
            )
            ->line(\Lang::getFromJson('If you did not create an account, no further action is required.'));
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
