<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

interface ConfigurationInterface
{
    /**
     * Return a configuration unit.
     *
     * @return \Quanta\Container\Configuration\ConfigurationUnitInterface
     */
    public function unit(): ConfigurationUnitInterface;
}
