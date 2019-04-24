<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

interface ConfigurationSourceInterface
{
    /**
     * Return a configuration.
     *
     * @return \Quanta\Container\Configuration\ConfigurationInterface
     */
    public function configuration(): ConfigurationInterface;
}
