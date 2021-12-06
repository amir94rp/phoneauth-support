<?php

namespace PhoneAuth\Support\Contracts;

use Closure;

interface VerificationBroker
{
    /**
     * Constant representing a successfully sent token.
     *
     * @var string
     */
    const VERIFICATION_TOKEN_SENT = 'phoneauth.sent';

    /**
     * Constant representing a successfully verified number.
     *
     * @var string
     */
    const NUMBER_VERIFIED = 'phoneauth.verified';

    /**
     * Constant representing the user not found response.
     *
     * @var string
     */
    const INVALID_USER = 'phoneauth.user';

    /**
     * Constant representing an invalid token.
     *
     * @var string
     */
    const INVALID_TOKEN = 'phoneauth.token';

    /**
     * Constant representing a throttled verification attempt.
     *
     * @var string
     */
    const VERIFICATION_THROTTLED = 'phoneauth.throttled';

    /**
     * Send a verification token to a user.
     *
     * @param  array  $credentials
     * @param Closure|null  $callback
     * @return string
     */
    public function sendVerificationToken(array $credentials, Closure $callback = null);

    /**
     * Verify the given token.
     *
     * @param array $credentials
     * @return mixed
     */
    public function verifyToken(array $credentials);
}
