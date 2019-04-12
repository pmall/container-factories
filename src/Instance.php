<?php declare(strict_types=1);

namespace Quanta\Container;

use Psr\Container\ContainerInterface;

final class Instance implements FactoryInterface
{
    /**
     * The name of the class to instantiate.
     *
     * @var string
     */
    private $class;

    /**
     * The factories used to produce the constructor arguments.
     *
     * @var \Quanta\Container\FactoryInterface[]
     */
    private $factories;

    /**
     * Constructor.
     *
     * @param string                                $class
     * @param \Quanta\Container\FactoryInterface    ...$factories
     */
    public function __construct(string $class, FactoryInterface ...$factories)
    {
        $this->class = $class;
        $this->factories = $factories;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container)
    {
        return new $this->class(...array_map(function ($factory) use ($container) {
            return $factory($container);
        }, $this->factories));
    }

    /**
     * @inheritdoc
     */
    public function compiled(string $container, callable $compiler): string
    {
        if (count($this->factories) == 0) {
            return sprintf('new %s', $this->class);
        }

        return implode(PHP_EOL, [
            sprintf('new %s(', $this->class),
            new Formatting\IndentedString(implode(',' . PHP_EOL, array_map(function ($factory) use ($container, $compiler) {
                return $factory->compiled($container, $compiler);
            }, $this->factories))),
            ')',
        ]);
    }
}
