<?php declare(strict_types=1);

namespace Quanta\Container;

interface ConfigurationInterface
{
    /**
     * Return an array of tagged service providers.
     *
     * @return \Quanta\Container\TaggedServiceProviderInterface[]
     */
    public function providers(): array;
}
