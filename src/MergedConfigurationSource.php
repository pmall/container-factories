<?php declare(strict_types=1);

namespace Quanta\Container;

final class MergedConfigurationSource implements ConfigurationSourceInterface
{
    /**
     * The array of configuration sources to merge.
     *
     * @var \Quanta\Container\ConfigurationSourceInterface[]
     */
    private $sources;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\ConfigurationSourceInterface ...$sources
     */
    public function __construct(ConfigurationSourceInterface ...$sources)
    {
        $this->sources = $sources;
    }

    /**
     * @inheritdoc
     */
    public function configuration(): ConfigurationInterface
    {
        $configurations = array_map([$this, 'plucked'], $this->sources);

        $maps = array_map([$this, 'pluckedMap'], $configurations);
        $passes = array_map([$this, 'pluckedPasses'], $configurations);

        return new Configuration(
            new MergedFactoryMap(...$maps),
            ...array_merge([], ...$passes)
        );
    }

    /**
     * Return configuration provided by the given source.
     *
     * @param \Quanta\Container\ConfigurationSourceInterface $source
     * @return \Quanta\Container\ConfigurationInterface
     */
    private function plucked(ConfigurationSourceInterface $source): ConfigurationInterface
    {
        return $source->configuration();
    }

    /**
     * Return factory map provided by the given configuration.
     *
     * @param \Quanta\Container\ConfigurationInterface $configuration
     * @return \Quanta\Container\FactoryMapInterface
     */
    private function pluckedMap(ConfigurationInterface $configuration): FactoryMapInterface
    {
        return $configuration->map();
    }

    /**
     * Return configuration passes provided by the given configuration.
     *
     * @param \Quanta\Container\ConfigurationInterface $configuration
     * @return \Quanta\Container\ConfigurationPassInterface[]
     */
    private function pluckedPasses(ConfigurationInterface $configuration): array
    {
        return $configuration->passes();
    }
}
