<?php declare(strict_types=1);

namespace Quanta\Container;

interface ConfigurationEntryInterface
{
    /**
     * Return the factories provided by the configuration entry.
     *
     * @return \Quanta\Container\FactoryMapInterface
     */
    public function factories(): FactoryMapInterface;

    /**
     * Return the extensions provided by the configuration entry.
     *
     * @return \Quanta\Container\FactoryMapInterface
     */
    public function extensions(): FactoryMapInterface;

    /**
     * Return the tags describing the factories provided by the configuration
     * entry.
     *
     * @return array[]
     */
    public function tags(): array;
}
