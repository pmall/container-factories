<?php declare(strict_types=1);

namespace Quanta\Container\Passes;

interface ProcessingPassInterface
{
    /**
     * Return an associative array of factories from the given ids.
     *
     * @param string ...$ids
     * @return callable[]
     */
    public function processed(string ...$ids): array;
}
