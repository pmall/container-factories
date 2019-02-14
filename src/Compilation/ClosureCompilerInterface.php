<?php declare(strict_types=1);

namespace Quanta\Container\Compilation;

interface ClosureCompilerInterface
{
    /**
     * Return a strign representation of the given closure.
     *
     * @param \Closure $closure
     * @return string
     */
    public function compiled(\Closure $closure): string;
}
