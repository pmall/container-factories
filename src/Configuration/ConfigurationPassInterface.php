<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

interface ConfigurationPassInterface
{
    /**
     * Return an associative array of factories from the given associative array
     * of factories and metadata.
     *
     * @param callable[]                                $factories
     * @param \Quanta\Container\Configuration\Metadata  $metadata
     * @return callable[]
     */
    public function factories(array $factories, Metadata $metadata): array;
}
