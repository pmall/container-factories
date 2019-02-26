<?php declare(strict_types=1);

namespace Quanta\Container\Factories;

use Psr\Container\ContainerInterface;

final class EmptyArrayFactory implements CompilableFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container)
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function compiled(Compiler $compiler): CompiledFactory
    {
        return new CompiledFactory('container', '', 'return [];');
    }
}
