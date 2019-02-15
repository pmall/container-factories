<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\FactoryMapInterface;

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
     * @return \Quanta\Container\Configuration\ConfigurationPassInterface[]
     */
    public function passes(): array;
}
