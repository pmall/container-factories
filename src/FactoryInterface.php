<?php declare(strict_types=1);

namespace Quanta\Container;

use Psr\Container\ContainerInterface;

interface FactoryInterface
{
    /**
     * Return the value produced by the factory with the given container.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @return mixed
     */
    public function __invoke(ContainerInterface $container);

    /**
     * Return a string representation of the value produced by the factory.
     *
     * The produced string must represent a single expression.
     *
     * @param string    $container  the container variable name
     * @param callable  $compiler   the compiler for recursive compilation
     * @return string
     */
    public function compiled(string $container, callable $compiler): string;
}
