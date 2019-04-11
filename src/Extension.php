<?php declare(strict_types=1);

namespace Quanta\Container;

use Psr\Container\ContainerInterface;

final class Extension implements FactoryInterface
{
    /**
     * The factory to extend.
     *
     * @var callable
     */
    private $factory;

    /**
     * The extension.
     *
     * @var callable
     */
    private $extension;

    /**
     * Constructor.
     *
     * @param callable $factory
     * @param callable $extension
     */
    public function __construct(callable $factory, callable $extension)
    {
        $this->factory = $factory;
        $this->extension = $extension;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container)
    {
        return ($this->extension)($container, ($this->factory)($container));
    }

    /**
     * @inheritdoc
     */
    public function compiled(string $container, callable $compiler): string
    {
        return vsprintf('(%s)($%s, (%s)($%s))', [
            $compiler($this->extension),
            $container,
            $compiler($this->factory),
            $container,
        ]);
    }
}
