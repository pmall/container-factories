<?php declare(strict_types=1);

namespace Quanta\Container\Factories;

use Psr\Container\ContainerInterface;

final class Invokable implements CompilableFactoryInterface
{
    /**
     * The invokable class name.
     *
     * @var string
     */
    private $class;

    /**
     * Return a new Invokable from the given class name.
     *
     * @param string $class
     * @return \Quanta\Container\Factories\Invokable
     */
    public static function instance(string $class): Invokable
    {
        return new self($class);
    }

    /**
     * Constructor.
     *
     * @param string $class
     */
    public function __construct(string $class)
    {
        $this->class = $class;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container)
    {
        return (new $this->class)($container);
    }

    /**
     * @inheritdoc
     */
    public function compiled(Compiler $compiler): CompiledFactory
    {
        return new CompiledFactory('container', ...[
            sprintf('return (new \%s)($container);', $this->class),
        ]);
    }
}
