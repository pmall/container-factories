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
    public function unit(): ConfigurationUnitInterface
    {
        return new MergedConfigurationUnit(...array_map(function ($configuration) {
            return $configuration->unit();
        }, $this->configurations));
    }
}
