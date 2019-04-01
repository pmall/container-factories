<?php declare(strict_types=1);

namespace Quanta\Container;

use Psr\Container\ContainerInterface;

use Quanta\Container\Compilation\CompilableInterface;

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
     * Return a compilable value representing the value produced by the factory.
     *
     * The container variable name to use is given as argument.
     *
     * @param string $container
     * @return \Quanta\Container\Compilation\CompilableInterface
     */
    public function compilable(string $container): CompilableInterface;
}
