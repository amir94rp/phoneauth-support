<?php

namespace PhoneAuth\Support\Traits;

use App\Notifications\VerifyPhoneNumber;

trait MustVerifyNumber
{
    /**
     * Determine if the user has verified their phone number.
     *
     * @return bool
     */
    public function hasVerifiedNumber()
    {
        return ! is_null($this->number_verified_at);
    }

    /**
     * Mark the given user's number as verified.
     *
     * @return bool
     */
    public function markNumberAsVerified()
    {
        return $this->forceFill([
            'number_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Send the text verification notification.
     *
     * @param $token
     * @return void
     */
    public function sendNumberVerificationNotification($token)
    {
        $this->notify(new VerifyPhoneNumber($token));
    }

    /**
     * Get the phone number that should be used for verification.
     *
     * @return string
     */
    public function getNumberForVerification()
    {
        return $this->number;
    }
}
