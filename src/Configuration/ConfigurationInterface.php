<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

interface ConfigurationInterface
{
    /**
     * Return the configuration entry provided by the configuration.
     *
     * @return \Quanta\Container\Configuration\ConfigurationEntry
     */
    public function entry(): ConfigurationEntry;
}
