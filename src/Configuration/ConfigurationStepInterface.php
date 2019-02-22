<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\FactoryMapInterface;

interface ConfigurationStepInterface
{
    /**
     * Return a new factory map from the given one.
     *
     * @param \Quanta\Container\FactoryMapInterface $map
     * @return \Quanta\Container\FactoryMapInterface
     */
    public function map(FactoryMapInterface $map): FactoryMapInterface;
}
