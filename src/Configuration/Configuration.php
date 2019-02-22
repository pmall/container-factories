<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\FactoryMapInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * The factory map.
     *
     * @var \Quanta\Container\FactoryMapInterface
     */
    private $map;

    /**
     * The array of configuration passes used to process the associative array
     * of factories.
     *
     * @var \Quanta\Container\Configuration\ConfigurationPassInterface[]
     */
    private $passes;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\FactoryMapInterface                         $map
     * @param \Quanta\Container\Configuration\ConfigurationPassInterface    ...$passes
     */
    public function __construct(FactoryMapInterface $map, ConfigurationPassInterface ...$passes)
    {
        $this->map = $map;
        $this->passes = $passes;
    }

    /**
     * @inheritdoc
     */
    public function step(ConfigurationStepInterface $step): ConfigurationStepInterface
    {
        return new ProcessingStep(
            new OverridingStep($step, $this->map),
            ...$this->passes
        );
    }
}
