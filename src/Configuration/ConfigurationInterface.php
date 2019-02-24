<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\ProcessedFactoryMap;

interface ConfigurationInterface
{
    /**
     * Return the processed factory map provided by the configuration.
     *
     * @return \Quanta\Container\ProcessedFactoryMap
     */
    public function map(): ProcessedFactoryMap;
}
