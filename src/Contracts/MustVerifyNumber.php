<?php

namespace PhoneAuth\Support\Contracts;

interface MustVerifyNumber
{
    /**
     * Determine if the user has verified their phone number.
     *
     * @return bool
     */
    public function hasVerifiedNumber();

    /**
     * Mark the given user's number as verified.
     *
     * @return bool
     */
    public function markNumberAsVerified();

    /**
     * Send the text verification notification.
     *
     * @param $token
     * @return void
     */
    public function sendNumberVerificationNotification($token);

    /**
     * Get the phone number that should be used for verification.
     *
     * @return string
     */
    public function getNumberForVerification();
}
