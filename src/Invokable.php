<?php declare(strict_types=1);

namespace Quanta\Container;

use Psr\Container\ContainerInterface;

use Quanta\Container\Compilation\CompiledString;
use Quanta\Container\Compilation\CompilableInterface;

final class Invokable implements FactoryInterface
{
    /**
     * The invokable class name.
     *
     * @var string
     */
    private $class;

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
    public function compilable(string $container): CompilableInterface
    {
        return new CompiledString(sprintf('(new %s)($%s)', $this->class, $container));
    }
}
