<?php


namespace PhoneAuth\Support\Contracts;


interface CanResetPassword
{
    /**
     * Get the phone number where verification token is sent.
     *
     * @return string
     */
    public function getNumberForPasswordReset();

    /**
     * Send the verification token.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token);
}
