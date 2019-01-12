<?php declare(strict_types=1);

namespace Quanta\Container;

use Interop\Container\ServiceProviderInterface;

interface TaggedServiceProviderInterface
{
    /**
     * Return the factory map of the provided factories.
     *
     * @return \Quanta\Container\FactoryMapInterface
     */
    public function factories(): FactoryMapInterface;

    /**
     * Return the factory map of the provided extensions.
     *
     * @return \Quanta\Container\FactoryMapInterface
     */
    public function extensions(): FactoryMapInterface;

    /**
     * Return an array of tags describing the provided container entries.
     *
     * @return array[]
     */
    public function tags(): array;
}
