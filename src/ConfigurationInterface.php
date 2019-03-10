<?php declare(strict_types=1);

namespace Quanta\Container;

interface ConfigurationInterface
{
    /**
     * Return the configuration entry provided by the configuration.
     *
     * @return \Quanta\Container\ConfiguredFactoryMap
     */
    public function map(): ConfiguredFactoryMap;
}
