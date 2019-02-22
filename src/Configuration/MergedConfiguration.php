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
    public function step(ConfigurationStepInterface $step): ConfigurationStepInterface
    {
        return array_reduce($this->configurations, [$this, 'reduced'], $step);
    }

    /**
     * Return the configuration step provided by the given configuration.
     *
     * @param \Quanta\Container\Configuration\ConfigurationStepInterface    $step
     * @param \Quanta\Container\Configuration\ConfigurationInterface        $configuration
     */
    private function reduced(
        ConfigurationStepInterface $step,
        ConfigurationInterface $configuration
    ): ConfigurationStepInterface {
        return $configuration->step($step);
    }
}
