<?php

namespace PhoneAuth\Support\Helpers\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class TextMessageChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $response = $notification->toTextMessage($notifiable);
        $url = config('phoneauth.channels.sms.url') . $response['url'];
        $APIKey = config('phoneauth.channels.sms.key');

        Http::accept('application/json')
            ->withHeaders([
                'Authorization' => 'AccessKey ' . $APIKey
            ])->post($url, $response['message']);
    }
}
