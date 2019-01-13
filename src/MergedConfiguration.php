<?php declare(strict_types=1);

namespace Quanta\Container;

final class MergedConfiguration implements ConfigurationInterface
{
    /**
     * The configuration to treat as a single one.
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
    public function providers(): array
    {
        $providers = array_map([$this, 'mapped'], $this->configurations);

        return array_merge([], ...$providers);
    }

    /**
     * Return the array of tagged service providers provided by the given
     * configuration.
     *
     * @param \Quanta\Container\ConfigurationInterface $configuration
     * @return \Quanta\Container\TaggedServiceProviderInterface[]
     */
    public function mapped(ConfigurationInterface $configuration): array
    {
        return $configuration->providers();
    }
}
