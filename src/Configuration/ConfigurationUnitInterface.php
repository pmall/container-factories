<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\FactoryMapInterface;
use Quanta\Container\Configuration\Passes\ProcessingPassInterface;

interface ConfigurationUnitInterface
{
    /**
     * Return a factory map.
     *
     * @return \Quanta\Container\FactoryMapInterface
     */
    public function map(): FactoryMapInterface;

    /**
     * Return a processing pass.
     *
     * @return \Quanta\Container\Configuration\Passes\ProcessingPassInterface
     */
    public function pass(): ProcessingPassInterface;
}
