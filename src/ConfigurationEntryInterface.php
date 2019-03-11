<?php declare(strict_types=1);

namespace Quanta\Container;

interface ConfigurationEntryInterface
{
    /**
     * Return a configuration.
     *
     * @return \Quanta\Container\Configuration
     */
    public function configuration(): Configuration;
}
