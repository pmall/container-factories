<?php declare(strict_types=1);

namespace Quanta\Container;

use Psr\Container\ContainerInterface;

final class FactoryArray implements FactoryInterface
{
    /**
     * The array of factories.
     *
     * @var \Quanta\Container\FactoryInterface[]
     */
    private $factories;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\FactoryInterface[] $factories
     * @throws \InvalidArgumentException
     */
    public function __construct(array $factories)
    {
        $result = \Quanta\ArrayTypeCheck::result($factories, FactoryInterface::class);

        if (! $result->isValid()) {
            throw new \InvalidArgumentException(
                $result->message()->constructor($this, 1)
            );
        }

        $this->factories = $factories;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container)
    {
        return array_map(function ($factory) use ($container) {
            return $factory($container);
        }, $this->factories);
    }

    /**
     * @inheritdoc
     */
    public function compiled(string $container, callable $compiler): string
    {
        return (string) new Formatting\StringArray(array_map(function ($factory) use ($container, $compiler) {
            return $factory->compiled($container, $compiler);
        }, $this->factories));
    }
}
