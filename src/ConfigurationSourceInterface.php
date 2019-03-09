<?php declare(strict_types=1);

namespace Quanta\Container;

interface ConfigurationSourceInterface
{
    /**
     * Return the configuration provided by the source.
     *
     * @return \Quanta\Container\ConfigurationInterface
     */
    public function configuration(): ConfigurationInterface;
}
