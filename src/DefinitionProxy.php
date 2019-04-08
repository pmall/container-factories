<?php declare(strict_types=1);

namespace Quanta\Container;

use Psr\Container\ContainerInterface;

use Quanta\Container\Compilation\CompilableInterface;

final class DefinitionProxy implements FactoryInterface
{
    /**
     * The factory definition.
     *
     * @var \Quanta\Container\DefinitionInterface
     */
    private $definition;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\DefinitionInterface $definition
     */
    public function __construct(DefinitionInterface $definition)
    {
        $this->definition = $definition;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container)
    {
        $factory = $this->definition->factory();

        return $factory($container);
    }

    /**
     * @inheritdoc
     */
    public function compilable(string $container): CompilableInterface
    {
        $factory = $this->definition->factory();

        return $factory->compilable($container);
    }
}
