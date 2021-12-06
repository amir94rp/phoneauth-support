<?php

namespace PhoneAuth\Support\Verification;

use Illuminate\Support\Facades\Facade;
use PhoneAuth\Support\Contracts\VerificationBroker;

/**
 * @method static mixed verifyToken(array $credentials)
 * @method static string sendVerificationToken(array $credentials, \Closure $callback = null)
 * @method static string createToken(\PhoneAuth\Auth\Contracts\MustVerifyNumber $user)
 * @method static void deleteToken(\PhoneAuth\Auth\Contracts\MustVerifyNumber $user)
 * @method static bool tokenExists(\PhoneAuth\Auth\Contracts\MustVerifyNumber $user, string $token)
 *
 * @see \PhoneAuth\Auth\Verification\VerificationBroker
 */

class PhoneNumber extends Facade
{
    /**
     * Constant representing a successfully sent token.
     *
     * @var string
     */
    const VERIFICATION_TOKEN_SENT = VerificationBroker::VERIFICATION_TOKEN_SENT;

    /**
     * Constant representing a successfully verified number.
     *
     * @var string
     */
    const NUMBER_VERIFIED = VerificationBroker::NUMBER_VERIFIED;

    /**
     * Constant representing the user not found response.
     *
     * @var string
     */
    const INVALID_USER = VerificationBroker::INVALID_USER;

    /**
     * Constant representing an invalid token.
     *
     * @var string
     */
    const INVALID_TOKEN = VerificationBroker::INVALID_TOKEN;

    /**
     * Constant representing a throttled reset attempt.
     *
     * @var string
     */
    const VERIFICATION_THROTTLED = VerificationBroker::VERIFICATION_THROTTLED;

    protected static function getFacadeAccessor()
    {
        return 'phoneauth.verification';
    }
}
