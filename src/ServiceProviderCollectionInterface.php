<?php declare(strict_types=1);

namespace Quanta\Container;

interface ServiceProviderCollectionInterface
{
    /**
     * Return an array of ServiceProviderInterface implementations.
     *
     * @return \Interop\Container\ServiceProviderInterface[]
     */
    public function providers(): array;
}
