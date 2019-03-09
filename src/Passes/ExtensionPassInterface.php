<?php declare(strict_types=1);

namespace Quanta\Container\Passes;

interface ExtensionPassInterface
{
    /**
     * Return an extended factory from the given id => factory pair.
     *
     * @param string    $id
     * @param callable  $factory
     * @return callable
     */
    public function extended(string $id, callable $factory): callable;
}
