<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

interface ConfigurationInterface
{
    /**
     * Return a configuration entry.
     *
     * @return \Quanta\Container\Configuration\ConfigurationEntry
     */
    public function entry(): ConfigurationEntry;
}
