<?php declare(strict_types=1);

namespace Quanta\Container\Compilation;

use Psr\Container\ContainerInterface;

use Quanta\Container\FactoryInterface;

final class CompilableFactory implements CompilableInterface
{
    /**
     * The factory to compile.
     *
     * @var callable
     */
    private $factory;

    /**
     * Constructor.
     *
     * @param callable $factory
     */
    public function __construct(callable $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @inheritdoc
     */
    public function compiled(Compiler $compiler): string
    {
        if ($this->factory instanceof FactoryInterface) {
            return vsprintf('function (%s $container) {%s%s%s}', [
                ContainerInterface::class,
                PHP_EOL,
                new IndentedString(vsprintf('return %s;', [
                    $this->factory->compilable('container')->compiled($compiler),
                ])),
                PHP_EOL,
            ]);
        }

        return $compiler($this->factory);
    }
}
