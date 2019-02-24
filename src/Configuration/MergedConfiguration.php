<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\MergedFactoryMap;
use Quanta\Container\ProcessedFactoryMap;
use Quanta\Container\FactoryMapInterface;

final class MergedConfiguration implements ConfigurationInterface
{
    /**
     * The array of configurations to merge.
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
    public function map(): ProcessedFactoryMap
    {
        $maps = array_map([$this, 'plucked'], $this->configurations);

        return new ProcessedFactoryMap(
            new MergedFactoryMap(...array_map([$this, 'pluckedMap'], $maps)),
            ...array_merge([], ...array_map([$this, 'pluckedPasses'], $maps))
        );
    }

    /**
     * Return the processed factory map provided by the given configuration.
     *
     * @param \Quanta\Container\Configuration\ConfigurationInterface $configuration
     * @return \Quanta\Container\ProcessedFactoryMap
     */
    private function plucked(ConfigurationInterface $configuration): ProcessedFactoryMap
    {
        return $configuration->map();
    }

    /**
     * Return the factory map provided by the given processed factory map.
     *
     * @param \Quanta\Container\ProcessedFactoryMap $map
     * @return \Quanta\Container\FactoryMapInterface
     */
    private function pluckedMap(ProcessedFactoryMap $map): FactoryMapInterface
    {
        return $map->map();
    }

    /**
     * Return the array of processing passes provided by the given processed
     * factory map.
     *
     * @param \Quanta\Container\ProcessedFactoryMap $map
     * @return \Quanta\Container\ProcessingPassInterface[]
     */
    private function pluckedPasses(ProcessedFactoryMap $map): array
    {
        return $map->passes();
    }
}
