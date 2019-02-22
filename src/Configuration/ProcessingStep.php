<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\FactoryMapInterface;
use Quanta\Container\ProcessedFactoryMap;

final class ProcessingStep implements ConfigurationStepInterface
{
    /**
     * The configuration step.
     *
     * @var \Quanta\Container\Configuration\ConfigurationStepInterface
     */
    private $step;

    /**
     * The compilation passes used to process the factory map.
     *
     * @var \Quanta\Container\Configuration\ConfigurationPassInterface[]
     */
    private $passes;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Configuration\ConfigurationStepInterface $step
     * @param \Quanta\Container\Configuration\ConfigurationPassInterface ...$passes
     */
    public function __construct(ConfigurationStepInterface $step, ConfigurationPassInterface ...$passes)
    {
        $this->step = $step;
        $this->passes = $passes;
    }

    /**
     * @inheritdoc
     */
    public function map(FactoryMapInterface $map): FactoryMapInterface
    {
        $map = $this->step->map($map);

        return new ProcessedFactoryMap($map, ...$this->passes);
    }
}
