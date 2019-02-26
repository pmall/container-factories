<?php declare(strict_types=1);

namespace Quanta\Container\Factories;

use Psr\Container\ContainerInterface;

interface CompilableFactoryInterface
{
    /**
     * Return the container entry.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @return mixed
     */
    public function __invoke(ContainerInterface $container);

    /**
     * Return a compiled factory.
     *
     * The compiler is given for recursive compilation of factories.
     *
     * @param \Quanta\Container\Factories\Compiler $compiler
     * @return \Quanta\Container\Factories\CompiledFactory
     */
    public function compiled(Compiler $compiler): CompiledFactory;
}
