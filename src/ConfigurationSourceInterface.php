<?php declare(strict_types=1);

namespace Quanta\Container;

interface ConfigurationSourceInterface
{
    /**
     * Return a configuration entry.
     *
     * @return \Quanta\Container\ConfigurationEntryInterface
     */
    public function entry(): ConfigurationEntryInterface;
}
