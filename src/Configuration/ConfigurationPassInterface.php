<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

interface ConfigurationPassInterface
{
    /**
     * Return a new associative array of factories from the given one.
     *
     * @param callable[] $factories
     * @return callable[]
     */
    public function processed(array $factories): array;
}
