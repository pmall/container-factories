<?php declare(strict_types=1);

namespace Quanta\Container\Compilation;

interface CompilableInterface
{
    /**
     * Return a string representation of the object.
     *
     * The compiler is given for recursive compilation.
     *
     * @param \Quanta\Container\Compilation\Compiler $compiler
     * @return string
     */
    public function compiled(Compiler $compiler): string;
}
