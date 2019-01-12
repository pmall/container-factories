<?php declare(strict_types=1);

namespace Quanta\Container;

use Interop\Container\ServiceProviderInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * The service providers to return.
     *
     * @var \Interop\Container\ServiceProviderInterface[]
     */
    private $providers;

    /**
     * Constructor.
     *
     * @param \Interop\Container\ServiceProviderInterface ...$providers
     */
    public function __construct(ServiceProviderInterface ...$providers)
    {
        $this->providers = $providers;
    }

    /**
     * @inheritdoc
     */
    public function providers(): array
    {
        return array_map([$this, 'provider'], $this->providers);
    }

    /**
     * Return a tagged service provider from the given service provider.
     *
     * @param \Interop\Container\ServiceProviderInterface $provider
     * @return \Quanta\Container\ExternalServiceProvider
     */
    private function provider(ServiceProviderInterface $provider): ExternalServiceProvider
    {
        return new ExternalServiceProvider($provider);
    }
}
