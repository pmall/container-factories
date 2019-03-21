<?php declare(strict_types=1);

namespace Quanta\Container;

interface ConfigurationSourceInterface
{
    /**
     * Return a configuration.
     *
     * @return \Quanta\Container\ConfigurationInterface
     */
    public function configuration(): ConfigurationInterface;
}
