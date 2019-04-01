<?php declare(strict_types=1);

namespace Quanta\Container;

use Psr\Container\ContainerInterface;

use Quanta\Container\Compilation\Compilable;
use Quanta\Container\Compilation\CompilableInterface;

final class FactoryArray implements FactoryInterface
{
    /**
     * The array of factories.
     *
     * @var \Quanta\Container\FactoryInterface[]
     */
    private $factories;

    /**
     * Return a new FactoryArray from the given array of factories.
     *
     * @param \Quanta\Container\FactoryInterface[] $factories
     * @return \Quanta\Container\FactoryArray
     * @throws \InvalidArgumentException
     */
    public static function instance(array $factories): self
    {
        $result = \Quanta\ArrayTypeCheck::result($factories, FactoryInterface::class);

        if (! $result->isValid()) {
            throw new \InvalidArgumentException(
                $result->message()->static(self::class, 'instance', 1)
            );
        }

        return new self($factories);
    }

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
    public function compilable(string $container): CompilableInterface
    {
        return new Compilable(array_map(function ($factory) use ($container) {
            return $factory->compilable($container);
        }, $this->factories));
    }
}
