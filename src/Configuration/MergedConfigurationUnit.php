<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\MergedFactoryMap;
use Quanta\Container\FactoryMapInterface;
use Quanta\Container\MergedProcessingPass;
use Quanta\Container\ProcessingPassInterface;

final class MergedConfigurationUnit implements ConfigurationUnitInterface
{
    /**
     * The array of configuration units to merge.
     *
     * @var \Quanta\Container\Configuration\ConfigurationUnitInterface[]
     */
    private $units;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Configuration\ConfigurationUnitInterface ...$units
     */
    public function __construct(ConfigurationUnitInterface ...$units)
    {
        $this->units = $units;
    }

    /**
     * @inheritdoc
     */
    public function map(): FactoryMapInterface
    {
        return new MergedFactoryMap(
            ...array_map(function ($unit) {
                return $unit->map();
            }, $this->units)
        );
    }

    /**
     * @inheritdoc
     */
    public function pass(): ProcessingPassInterface
    {
        return new MergedProcessingPass(
            ...array_map(function ($unit) {
                return $unit->pass();
            }, $this->units)
        );
    }
}
