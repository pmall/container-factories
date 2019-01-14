<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Passes\ConfigurationPassInterface;

final class ConfigurationFactoryMap implements FactoryMapInterface
{
    /**
     * The configuration.
     *
     * @var \Quanta\Container\ConfigurationInterface
     */
    private $configuration;

    /**
     * The configuration passes to apply.
     *
     * @var \Quanta\Container\Passes\ConfigurationPassInterface[]
     */
    private $passes;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\ConfigurationInterface              $configuration
     * @param \Quanta\Container\Passes\ConfigurationPassInterface   ...$passes
     */
    public function __construct(ConfigurationInterface $configuration, ConfigurationPassInterface ...$passes)
    {
        $this->configuration = $configuration;
        $this->passes = $passes;
    }

    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        $entries = $this->configuration->entries();

        $factories = array_map([$this, 'factoryMap'], $entries);
        $extensions = array_map([$this, 'extensionMap'], $entries);
        $metadata = array_map([$this, 'metadata'], $entries);

        $map = new ExtendedFactoryMap(
            new MergedFactoryMap(...$factories), ...$extensions
        );

        $factories = $map->factories();
        $metadata = new Metadata(...$metadata);

        foreach ($this->passes as $pass) {
            $processed[] = $pass->factories($factories, $metadata);
        }

        return array_merge($factories, ...($processed ?? []));
    }

    /**
     * Return the factory map provided by the given configuration entry.
     *
     * @param \Quanta\Container\ConfigurationEntryInterface $entry
     * @return \Quanta\Container\FactoryMapInterface
     */
    private function factoryMap(ConfigurationEntryInterface $entry): FactoryMapInterface
    {
        return $entry->factories();
    }

    /**
     * Return the extension map provided by the given configuration entry.
     *
     * @param \Quanta\Container\ConfigurationEntryInterface $entry
     * @return \Quanta\Container\FactoryMapInterface
     */
    private function extensionMap(ConfigurationEntryInterface $entry): FactoryMapInterface
    {
        return $entry->extensions();
    }

    /**
     * Return the metadata provided by the given configuration entry.
     *
     * @param \Quanta\Container\ConfigurationEntryInterface $entry
     * @return array[]
     */
    private function metadata(ConfigurationEntryInterface $entry): array
    {
        return $entry->metadata();
    }
}
