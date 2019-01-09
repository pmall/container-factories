<?php declare(strict_types=1);

namespace Quanta\Container;

use Interop\Container\ServiceProviderInterface;

use Quanta\Exceptions\ReturnTypeErrorMessage;
use Quanta\Exceptions\ArrayReturnTypeErrorMessage;

final class ConfigurationFactoryMap implements FactoryMapInterface
{
    /**
     * The configurations.
     *
     * @var \Quanta\Container\ConfigurationInterface[]
     */
    private $configurations;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\ConfigurationInterface ...$configurations
     */
    public function __construct(ConfigurationInterface ...$configurations)
    {
        $this->configurations = $configurations;
    }

    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        $providers = array_map([$this, 'providers'], $this->configurations);
        $providers = array_merge([], ...$providers);

        $map = new ExtendedFactoryMap(
            new MergedFactoryMap(
                ...array_map([$this, 'factoryMap'], $providers)
            ),
            ...array_map([$this, 'extensionMap'], $providers)
        );

        return $map->factories();
    }

    /**
     * Return the service providers provided by the given configuration.
     *
     * @param \Quanta\Container\ConfigurationInterface $configuration
     * @return \Interop\Container\ServiceProviderInterface[]
     */
    private function providers(ConfigurationInterface $configuration): array
    {
        return $configuration->providers();
    }

    /**
     * Return the factory map of the given service provider.
     *
     * @param \Interop\Container\ServiceProviderInterface $provider
     * @return \Quanta\Container\FactoryMap
     * @throws \UnexpectedValueException
     */
    private function factoryMap(ServiceProviderInterface $provider): FactoryMap
    {
        $factories = $provider->getFactories();

        if (! is_array($factories)) {
            throw new \UnexpectedValueException(
                (string) new ReturnTypeErrorMessage(
                    sprintf('%s::getFactories()', get_class($provider)), 'array', $factories
                )
            );
        }

        try {
            return new FactoryMap($factories);
        }

        catch (\InvalidArgumentException $e) {
            throw new \UnexpectedValueException(
                (string) new ArrayReturnTypeErrorMessage(
                    sprintf('%s::getFactories()', get_class($provider)), 'callable', $factories
                )
            );
        }
    }

    /**
     * Return the extension map of the given service provider.
     *
     * @param \Interop\Container\ServiceProviderInterface $provider
     * @return \Quanta\Container\FactoryMap
     * @throws \UnexpectedValueException
     */
    private function extensionMap(ServiceProviderInterface $provider): FactoryMap
    {
        $extensions = $provider->getExtensions();

        if (! is_array($extensions)) {
            throw new \UnexpectedValueException(
                (string) new ReturnTypeErrorMessage(
                    sprintf('%s::getExtensions()', get_class($provider)), 'array', $extensions
                )
            );
        }

        try {
            return new FactoryMap($extensions);
        }

        catch (\InvalidArgumentException $e) {
            throw new \UnexpectedValueException(
                (string) new ArrayReturnTypeErrorMessage(
                    sprintf('%s::getExtensions()', get_class($provider)), 'callable', $extensions
                )
            );
        }
    }
}
