<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

interface ConfigurationSourceInterface
{
    /**
     * Return the configuration provided by the source.
     *
     * @return \Quanta\Container\Configuration\ConfigurationInterface
     */
    public function configuration(): ConfigurationInterface;
}
