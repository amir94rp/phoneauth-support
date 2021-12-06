<?php

namespace PhoneAuth\Support\Contracts;

use PhoneAuth\Support\Contracts\MustVerifyNumber as MustVerifyNumberContract;

interface TokenRepositoryInterface
{
    /**
     * Create a new token.
     *
     * @param MustVerifyNumber $user
     * @return string
     */
    public function create(MustVerifyNumberContract $user);

    /**
     * Determine if a token record exists and is valid.
     *
     * @param MustVerifyNumberContract $user
     * @param string $token
     * @return bool
     */
    public function exists(MustVerifyNumberContract $user, $token);

    /**
     * Determine if the given user recently created a verification token.
     *
     * @param MustVerifyNumberContract $user
     * @return bool
     */
    public function recentlyCreatedToken(MustVerifyNumberContract $user);

    /**
     * Delete a token record.
     *
     * @param MustVerifyNumberContract $user
     * @return void
     */
    public function delete(MustVerifyNumberContract $user);

    /**
     * Delete expired tokens.
     *
     * @return void
     */
    public function deleteExpired();
}
