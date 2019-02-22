<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

interface ConfigurationInterface
{
    /**
     * Return a new configuration step from the given one.
     *
     * @param \Quanta\Container\Configuration\ConfigurationStepInterface $step
     * @return \Quanta\Container\Configuration\ConfigurationStepInterface
     */
    public function step(ConfigurationStepInterface $step): ConfigurationStepInterface;
}
