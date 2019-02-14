<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Interop\Container\ServiceProviderInterface;

final class ServiceProviderCollection implements ConfigurationInterface
{
    /**
     * The array of service providers to return as configuration entries.
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
    public function entries(): array
    {
        return array_map([$this, 'entry'], $this->providers);
    }

    /**
     * Return an service provider configuration entry from the given service
     * provider.
     *
     * @param \Interop\Container\ServiceProviderInterface $provider
     * @return \Quanta\Container\Configuration\ServiceProviderAdapter
     */
    private function entry(ServiceProviderInterface $provider): ServiceProviderAdapter
    {
        return new ServiceProviderAdapter($provider);
    }
}
