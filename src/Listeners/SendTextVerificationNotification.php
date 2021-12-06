<?php

namespace PhoneAuth\Support\Listeners;

use Illuminate\Auth\Events\Registered;
use PhoneAuth\Support\Contracts\MustVerifyNumber;
use PhoneAuth\Support\Verification\PhoneNumber;

class SendTextVerificationNotification
{
    /**
     * Handle the event.
     *
     * @param Registered $event
     * @return void
     */
    public function handle(Registered $event)
    {
        if ($event->user instanceof MustVerifyNumber && ! $event->user->hasVerifiedNumber()) {
            PhoneNumber::sendVerificationToken([
                'number' => $event->user->getNumberForVerification()
            ]);
        }
    }
}
