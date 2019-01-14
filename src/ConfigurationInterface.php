<?php declare(strict_types=1);

namespace Quanta\Container;

interface ConfigurationInterface
{
    /**
     * Return an array of configuration entries.
     *
     * @return \Quanta\Container\ConfigurationEntryInterface[]
     */
    public function entries(): array;
}
