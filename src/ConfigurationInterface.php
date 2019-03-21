<?php declare(strict_types=1);

namespace Quanta\Container;

interface ConfigurationInterface
{
    /**
     * Return a configuration entry.
     *
     * @return \Quanta\Container\ConfigurationEntry
     */
    public function entry(): ConfigurationEntry;
}
