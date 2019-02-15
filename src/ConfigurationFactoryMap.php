<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Configuration\Metadata;
use Quanta\Container\Configuration\ConfigurationInterface;
use Quanta\Container\Configuration\ConfigurationEntryInterface;

final class ConfigurationFactoryMap implements FactoryMapInterface
{
    /**
     * The configuration.
     *
     * @var \Quanta\Container\Configuration\ConfigurationInterface[]
     */
    private $configurations;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Configuration\ConfigurationInterface ...$configurations
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
        $entries = array_map([$this, 'mapped'], $this->configurations);
        $entries = array_merge([], ...$entries);

        $factories = array_map([$this, 'factoryMap'], $entries);
        $extensions = array_map([$this, 'extensionMap'], $entries);
        $metadata = array_map([$this, 'metadata'], $entries);

        $map = new ExtendedFactoryMap(
            new MergedFactoryMap(...$factories), ...$extensions
        );

        $factories = $map->factories();
        $metadata = new Metadata(...$metadata);

        foreach ($entries as $entry) {
            foreach ($entry->passes() as $pass) {
                $processed[] = $pass->factories($factories, $metadata);
            }
        }

        return array_merge($factories, ...($processed ?? []));
    }

    /**
     * Return the configuration entries provided by the given configuration.
     *
     * @param \Quanta\Container\Configuration\ConfigurationInterface $configuration
     */
    private function mapped(ConfigurationInterface $configuration): array
    {
        return array_values($configuration->entries());
    }

    /**
     * Return the factory map provided by the given configuration entry.
     *
     * @param \Quanta\Container\Configuration\ConfigurationEntryInterface $entry
     * @return \Quanta\Container\FactoryMapInterface
     */
    private function factoryMap(ConfigurationEntryInterface $entry): FactoryMapInterface
    {
        return $entry->factories();
    }

    /**
     * Return the extension map provided by the given configuration entry.
     *
     * @param \Quanta\Container\Configuration\ConfigurationEntryInterface $entry
     * @return \Quanta\Container\FactoryMapInterface
     */
    private function extensionMap(ConfigurationEntryInterface $entry): FactoryMapInterface
    {
        return $entry->extensions();
    }

    /**
     * Return the metadata provided by the given configuration entry.
     *
     * @param \Quanta\Container\Configuration\ConfigurationEntryInterface $entry
     * @return array[]
     */
    private function metadata(ConfigurationEntryInterface $entry): array
    {
        return $entry->metadata();
    }

    /**
     * Return the configuration passes provided by the given configuration
     * entry.
     *
     * @param \Quanta\Container\Configuration\ConfigurationEntryInterface $entry
     * @return \Quanta\Container\Configuration\ConfigurationPassInterface[]
     */
    private function passes(ConfigurationEntryInterface $entry): array
    {
        return $entry->passes();
    }
}
