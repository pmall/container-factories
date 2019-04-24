<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

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
    public function factories(): array
    {
        return array_merge([], ...array_map(function ($configuration) {
            return $configuration->factories();
        }, $this->configurations));
    }

    /**
     * @inheritdoc
     */
    public function mappers(): array
    {
        $mappers = array_merge_recursive([], ...array_map(function ($configuration) {
            return $configuration->mappers();
        }, $this->configurations));

        return array_map(function ($mapper) {
            return is_array($mapper) ? new Tagging\CompositeTagging(...$mapper) : $mapper;
        }, $mappers);
    }

    /**
     * @inheritdoc
     */
    public function extensions(): array
    {
        return array_merge_recursive([], ...array_map(function ($configuration) {
            return $configuration->extensions();
        }, $this->configurations));
    }
}
