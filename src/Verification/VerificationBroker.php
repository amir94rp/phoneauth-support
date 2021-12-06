<?php

namespace PhoneAuth\Support\Verification;

use Closure;
use PhoneAuth\Support\Contracts\MustVerifyNumber as MustVerifyNumberContract;
use PhoneAuth\Support\Contracts\VerificationBroker as VerificationBrokerContract;
use PhoneAuth\Support\Verification\DatabaseTokenRepository;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Arr;

class VerificationBroker implements VerificationBrokerContract
{
    /**
     * The token repository.
     *
     * @var DatabaseTokenRepository
     */
    protected $tokens;

    /**
     * The user provider implementation.
     *
     * @var UserProvider
     */
    protected $users;

    /**
     * Create a new verification broker instance.
     *
     * @param DatabaseTokenRepository $tokens
     * @param UserProvider $users
     */
    public function __construct(DatabaseTokenRepository $tokens, UserProvider $users)
    {
        $this->users = $users;
        $this->tokens = $tokens;
    }

    /**
     * Send a verification token to a user.
     *
     * @param array $credentials
     * @param Closure|null $callback
     * @return string
     * @throws \UnexpectedValueException
     * @throws \Exception
     * @throws \Carbon\Exceptions\InvalidFormatException
     */
    public function sendVerificationToken(array $credentials, Closure $callback = null)
    {
        // First we will check to see if we found a user at the given credentials and
        // if we did not we will redirect back to this current URI with a piece of
        // "flash" data in the session to indicate to the developers the errors.
        $user = $this->getUser($credentials);

        if (is_null($user)) {
            return static::INVALID_USER;
        }

        if ($this->tokens->recentlyCreatedToken($user)) {
            return static::VERIFICATION_THROTTLED;
        }

        $token = $this->tokens->create($user);

        if ($callback) {
            $callback($user, $token);
        } else {
            // Once we have the verification token, we are ready to send the message out to this
            // user with a text message. We will then redirect back to
            // the current URI having nothing set in the session to indicate errors.
            $user->sendNumberVerificationNotification($token);
        }

        return static::VERIFICATION_TOKEN_SENT;
    }

    /**
     * Verify the given token.
     *
     * @param array $credentials
     * @param Closure $callback
     * @return mixed
     * @throws \Carbon\Exceptions\InvalidFormatException
     */
    public function verifyToken(array $credentials)
    {
        $user = $this->validateCredentials($credentials);

        // If the responses from the validate method is not a user instance, we will
        // assume that it is a redirect and simply return it from this method and
        // the user is properly redirected having an error message on the post.
        if (! $user instanceof MustVerifyNumberContract) {
            return $user;
        }

        $this->tokens->delete($user);

        return static::NUMBER_VERIFIED;
    }

    /**
     * Validate token for the given credentials.
     *
     * @param array $credentials
     * @return MustVerifyNumberContract|string
     * @throws \Carbon\Exceptions\InvalidFormatException
     */
    protected function validateCredentials(array $credentials)
    {
        if (is_null($user = $this->getUser($credentials))) {
            return static::INVALID_USER;
        }

        if (! $this->tokens->exists($user, $credentials['token'])) {
            return static::INVALID_TOKEN;
        }

        return $user;
    }

    /**
     * Get the user for the given credentials.
     *
     * @param  array  $credentials
     * @return MustVerifyNumberContract|null
     *
     */
    public function getUser(array $credentials)
    {
        $credentials = Arr::except($credentials, ['token']);

        $user = $this->users->retrieveByCredentials($credentials);

        return $user;
    }

    /**
     * Create a new verification token for the given user.
     *
     * @param MustVerifyNumberContract $user
     * @return string
     * @throws \Exception
     */
    public function createToken(MustVerifyNumberContract $user)
    {
        return $this->tokens->create($user);
    }

    /**
     * Delete verification tokens of the given user.
     *
     * @param  MustVerifyNumberContract  $user
     * @return void
     */
    public function deleteToken(MustVerifyNumberContract $user)
    {
        $this->tokens->delete($user);
    }

    /**
     * Validate the given verification token.
     *
     * @param MustVerifyNumberContract $user
     * @param string $token
     * @return bool
     * @throws \Carbon\Exceptions\InvalidFormatException
     */
    public function tokenExists(MustVerifyNumberContract $user, $token)
    {
        return $this->tokens->exists($user, $token);
    }

    /**
     * Get the verification token repository implementation.
     *
     * @return DatabaseTokenRepository
     */
    public function getRepository()
    {
        return $this->tokens;
    }
}
