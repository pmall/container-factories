<?php declare(strict_types=1);

namespace Quanta\Container;

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
        $entries = $this->configuration->entries();

        $map = new ExtendedFactoryMap(
            new MergedFactoryMap(
                ...array_map([$this, 'factoryMap'], $entries)
            ),
            ...array_map([$this, 'extensionMap'], $entries)
        );

        return $map->factories();
    }

    /**
     * Return the factory map provided by the given service provider.
     *
     * @param \Quanta\Container\ConfigurationEntryInterface $entry
     * @return \Quanta\Container\FactoryMapInterface
     */
    private function factoryMap(ConfigurationEntryInterface $entry): FactoryMapInterface
    {
        return $entry->factories();
    }

    /**
     * Return the extension map provided by the given service provider.
     *
     * @param \Quanta\Container\ConfigurationEntryInterface $entry
     * @return \Quanta\Container\FactoryMapInterface
     */
    private function extensionMap(ConfigurationEntryInterface $entry): FactoryMapInterface
    {
        return $entry->extensions();
    }

    /**
     * Return the extension map provided by the given service provider.
     *
     * @param \Quanta\Container\ConfigurationEntryInterface $entry
     * @return array[]
     */
    private function tags(ConfigurationEntryInterface $entry): array
    {
        return $entry->tags();
    }
}
