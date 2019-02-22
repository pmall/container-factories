<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\MergedFactoryMap;
use Quanta\Container\FactoryMapInterface;

final class OverridingStep implements ConfigurationStepInterface
{
    /**
     * The configuration step.
     *
     * @var \Quanta\Container\Configuration\ConfigurationStepInterface
     */
    private $step;

    /**
     * The factory maps to merge.
     *
     * @var \Quanta\Container\FactoryMapInterface[]
     */
    private $maps;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Configuration\ConfigurationStepInterface    $step
     * @param \Quanta\Container\FactoryMapInterface                         ...$maps
     */
    public function __construct(ConfigurationStepInterface $step, FactoryMapInterface ...$maps)
    {
        $this->step = $step;
        $this->maps = $maps;
    }

    /**
     * @inheritdoc
     */
    public function map(FactoryMapInterface $map): FactoryMapInterface
    {
        return $this->step->map(new MergedFactoryMap(
            ...array_merge($this->maps, [$map])
        ));
    }
}
