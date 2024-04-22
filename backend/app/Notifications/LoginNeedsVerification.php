<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;
use App\Traits\ClickSendSms;

class LoginNeedsVerification extends Notification
{
    use Queueable,ClickSendSms;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [TwilioChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toTwilio(object $notifiable)
    {
       try {
        $loginCode = rand(111111,999999);
        $notifiable->update([
           'login_code'=>$loginCode
        ]);


         return (new TwilioSmsMessage("Your login code is $loginCode \n dont share this with anyone! "));

       } catch (\Exception $e) {
          Log::error("An error occured during submition ".$e);
       }

       return response("the message has been successfully sent",200);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
