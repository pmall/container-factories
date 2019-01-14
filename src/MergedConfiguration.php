<?php declare(strict_types=1);

namespace Quanta\Container;

final class MergedConfiguration implements ConfigurationInterface
{
    /**
     * The array of configurations to merge.
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
    public function entries(): array
    {
        $entries = array_map([$this, 'mapped'], $this->configurations);

        return array_merge([], ...$entries);
    }

    /**
     * Return the array of configuration entries provided by the given
     * configuration.
     *
     * @param \Quanta\Container\ConfigurationInterface $configuration
     * @return \Quanta\Container\ConfigurationEntryInterface[]
     */
    public function mapped(ConfigurationInterface $configuration): array
    {
        return $configuration->entries();
    }
}
