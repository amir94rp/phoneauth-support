<?php

namespace PhoneAuth\Support\Traits;

trait CanResetPassword
{
    /**
     * Get the phone number where verification token is sent.
     *
     * @return string
     */
    public function getNumberForPasswordReset()
    {
        return $this->number;
    }

    /**
     * Send the verification token.
     *
     * @param $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->sendNumberVerificationNotification($token);
    }
}
