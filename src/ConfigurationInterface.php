<?php declare(strict_types=1);

namespace Quanta\Container;

interface ConfigurationInterface
{
    /**
     * Return the factory map provided by the configuration.
     *
     * @return \Quanta\Container\FactoryMapInterface
     */
    public function map(): FactoryMapInterface;

    /**
     * Return the array of configuration passes provided by the configuration.
     *
     * @return \Quanta\Container\ConfigurationPassInterface[]
     */
    public function passes(): array;
}
