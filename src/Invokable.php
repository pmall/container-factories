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
     * Return a new Invokable from the given class name.
     *
     * @param string $class
     * @return \Quanta\Container\Invokable
     */
    public static function instance(string $class): self
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
    public function compilable(string $container): CompilableInterface
    {
        return new CompiledString(sprintf('(new %s)($%s)', $this->class, $container));
    }
}
