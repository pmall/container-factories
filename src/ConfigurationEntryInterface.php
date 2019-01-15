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
     * Return the metadata associated to the factories.
     *
     * @return array[]
     */
    public function metadata(): array;

    /**
     * Return the configuration passes provided by the configuration entry.
     *
     * @return \Quanta\Container\Passes\ConfigurationPassInterface[]
     */
    public function passes(): array;
}
