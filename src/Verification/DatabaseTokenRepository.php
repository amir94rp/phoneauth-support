<?php

namespace PhoneAuth\Support\Verification;

use PhoneAuth\Support\Contracts\TokenRepositoryInterface;
use PhoneAuth\Support\Contracts\MustVerifyNumber as MustVerifyNumberContract;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Carbon;

class DatabaseTokenRepository implements TokenRepositoryInterface
{
    /**
     * The database connection instance.
     *
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * The Hasher implementation.
     *
     * @var HasherContract
     */
    protected $hasher;

    /**
     * The token database table.
     *
     * @var string
     */
    protected $table;

    /**
     * The number of seconds a token should last.
     *
     * @var int
     */
    protected $expires;

    /**
     * Minimum number of seconds before redefining the token.
     *
     * @var int
     */
    protected $throttle;

    /**
     * Create a new token repository instance.
     *
     * @param ConnectionInterface $connection
     * @param HasherContract $hasher
     * @param  string  $table
     * @param  int  $expires
     * @param  int  $throttle
     * @return void
     */
    public function __construct(ConnectionInterface $connection, HasherContract $hasher,
                                $table, $expires = 60, $throttle = 60)
    {
        $this->table = $table;
        $this->hasher = $hasher;
        $this->expires = $expires * 60;
        $this->connection = $connection;
        $this->throttle = $throttle;
    }

    /**
     * Create a new token record.
     *
     * @param MustVerifyNumberContract $user
     * @return string
     * @throws \Exception
     */
    public function create(MustVerifyNumberContract $user)
    {
        $number = $user->getNumberForVerification();

        $this->deleteExisting($user);

        // We will create a new, random token for the user so that we can text them
        // a safe token for verification. Then we will insert a record in
        // the database so that we can verify the token within the actual verification.
        $token = $this->createNewToken();

        $this->getTable()->insert($this->getPayload($number, $token));

        return $token;
    }

    /**
     * Delete all existing verification tokens from the database.
     *
     * @param MustVerifyNumberContract $user
     * @return int
     */
    protected function deleteExisting(MustVerifyNumberContract $user)
    {
        return $this->getTable()->where('number', $user->getNumberForVerification())->delete();
    }

    /**
     * Build the record payload for the table.
     *
     * @param $number
     * @param string $token
     * @return array
     * @throws \Exception
     */
    protected function getPayload($number, $token)
    {
        return ['number' => $number, 'token' => $this->hasher->make($token), 'created_at' => new Carbon];
    }

    /**
     * Determine if a token record exists and is valid.
     *
     * @param MustVerifyNumberContract $user
     * @param string $token
     * @return bool
     * @throws \Carbon\Exceptions\InvalidFormatException
     */
    public function exists(MustVerifyNumberContract $user, $token)
    {
        $record = (array) $this->getTable()->where(
            'number', $user->getNumberForVerification()
        )->first();

        return $record &&
            ! $this->tokenExpired($record['created_at']) &&
            $this->hasher->check($token, $record['token']);
    }

    /**
     * Determine if the token has expired.
     *
     * @param string $createdAt
     * @return bool
     * @throws \Carbon\Exceptions\InvalidFormatException
     */
    protected function tokenExpired($createdAt)
    {
        return Carbon::parse($createdAt)->addSeconds($this->expires)->isPast();
    }

    /**
     * Determine if the given user recently created a verification token.
     *
     * @param MustVerifyNumberContract $user
     * @return bool
     * @throws \Carbon\Exceptions\InvalidFormatException
     */
    public function recentlyCreatedToken(MustVerifyNumberContract $user)
    {
        $record = (array) $this->getTable()->where(
            'number', $user->getNumberForVerification()
        )->first();

        return $record && $this->tokenRecentlyCreated($record['created_at']);
    }

    /**
     * Determine if the token was recently created.
     *
     * @param string $createdAt
     * @return bool
     * @throws \Carbon\Exceptions\InvalidFormatException
     */
    protected function tokenRecentlyCreated($createdAt)
    {
        if ($this->throttle <= 0) {
            return false;
        }

        return Carbon::parse($createdAt)->addSeconds(
            $this->throttle
        )->isFuture();
    }

    /**
     * Delete a token record by user.
     *
     * @param  MustVerifyNumberContract  $user
     * @return void
     */
    public function delete(MustVerifyNumberContract $user)
    {
        $this->deleteExisting($user);
    }

    /**
     * Delete expired tokens.
     *
     * @return void
     */
    public function deleteExpired()
    {
        $expiredAt = Carbon::now()->subSeconds($this->expires);
        $this->getTable()->where('created_at', '<', $expiredAt)->delete();
    }

    /**
     * Create a new token for the user.
     *
     * @return string
     */
    public function createNewToken()
    {
        return rand(config('phoneauth.token.min' , 100000) ,
            config('phoneauth.token.max' , 999999));
    }

    /**
     * Get the database connection instance.
     *
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Begin a new database query against the table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getTable()
    {
        return $this->connection->table($this->table);
    }

    /**
     * Get the hasher instance.
     *
     * @return HasherContract
     */
    public function getHasher()
    {
        return $this->hasher;
    }
}
