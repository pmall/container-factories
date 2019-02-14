<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

interface ConfigurationInterface
{
    /**
     * Return an array of configuration entries.
     *
     * @return \Quanta\Container\Configuration\ConfigurationEntryInterface[]
     */
    public function entries(): array;
}
