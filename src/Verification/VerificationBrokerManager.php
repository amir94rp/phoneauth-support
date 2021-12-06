<?php

namespace PhoneAuth\Support\Verification;

use Illuminate\Contracts\Auth\PasswordBrokerFactory as FactoryContract;
use PhoneAuth\Support\Verification\DatabaseTokenRepository;
use PhoneAuth\Support\Verification\VerificationBroker;

class VerificationBrokerManager implements FactoryContract
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The array of created "drivers".
     *
     * @var array
     */
    protected $brokers = [];

    /**
     * Create a new VerificationBroker manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Attempt to get the broker from the local cache.
     *
     * @param  string|null  $name
     * @return \PhoneAuth\Auth\Verification\VerificationBroker
     */
    public function broker($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->brokers[$name] ?? ($this->brokers[$name] = $this->resolve($name));
    }

    /**
     * Resolve the given broker.
     *
     * @param  string  $name
     * @return \PhoneAuth\Auth\Verification\VerificationBroker
     *
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

//        if (is_null($config)) {
//            throw new InvalidArgumentException("Password resetter [{$name}] is not defined.");
//        }

        // The validation broker uses a token repository to validate tokens
        return new VerificationBroker(
            $this->createTokenRepository($config),
            $this->app['auth']->createUserProvider($config['provider'] ?? null)
        );
    }

    /**
     * Create a token repository instance based on the given configuration.
     *
     * @param  array  $config
     * @return \PhoneAuth\Auth\Verification\DatabaseTokenRepository
     */
    protected function createTokenRepository(array $config)
    {
        $connection = $config['connection'] ?? null;

        return new DatabaseTokenRepository(
            $this->app['db']->connection($connection),
            $this->app['hash'],
            $config['table'],
            $config['expire'],
            $config['throttle'] ?? 0
        );
    }

    /**
     * Get the verification broker configuration.
     *
     * @param  string  $name
     * @return array
     */
    protected function getConfig($name)
    {
        return $this->app['config']["auth.passwords.{$name}"];
    }

    /**
     * Get the default verification broker name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['auth.defaults.passwords'];
    }

    /**
     * Set the default verification broker name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['auth.defaults.passwords'] = $name;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->broker()->{$method}(...$parameters);
    }
}
