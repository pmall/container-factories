<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Exceptions\ReturnTypeErrorMessage;
use Quanta\Exceptions\ArrayReturnTypeErrorMessage;

final class ConfigurationFactoryMap implements FactoryMapInterface
{
    /**
     * The configuration.
     *
     * @var \Quanta\Container\ConfigurationInterface
     */
    private $configuration;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        $providers = $this->configuration->providers();

        $map = new ExtendedFactoryMap(
            new MergedFactoryMap(
                ...array_map([$this, 'factoryMap'], $providers)
            ),
            ...array_map([$this, 'extensionMap'], $providers)
        );

        return $map->factories();
    }

    /**
     * Return the factory map provided by the given service provider.
     *
     * @param \Quanta\Container\TaggedServiceProviderInterface $provider
     * @return \Quanta\Container\FactoryMapInterface
     */
    private function factoryMap(TaggedServiceProviderInterface $provider): FactoryMapInterface
    {
        return $provider->factories();
    }

    /**
     * Return the extension map provided by the given service provider.
     *
     * @param \Quanta\Container\TaggedServiceProviderInterface $provider
     * @return \Quanta\Container\FactoryMapInterface
     */
    private function extensionMap(TaggedServiceProviderInterface $provider): FactoryMapInterface
    {
        return $provider->extensions();
    }

    /**
     * Return the extension map provided by the given service provider.
     *
     * @param \Quanta\Container\TaggedServiceProviderInterface $provider
     * @return array[]
     */
    private function tags(TaggedServiceProviderInterface $provider): array
    {
        return $provider->tags();
    }
}
