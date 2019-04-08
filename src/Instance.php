<?php declare(strict_types=1);

namespace Quanta\Container;

use Psr\Container\ContainerInterface;

use Quanta\Container\Compilation\Template;
use Quanta\Container\Compilation\IndentedString;
use Quanta\Container\Compilation\CompiledString;
use Quanta\Container\Compilation\CompilableInterface;

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
    public function compilable(string $container): CompilableInterface
    {
        if (count($this->factories) == 0) {
            return new CompiledString(sprintf('new %s', $this->class));
        }

        $placeholders = array_pad([], count($this->factories), '%s');

        $tpl = vsprintf('new %s(%s%s%s)', [
            $this->class,
            PHP_EOL,
            new IndentedString(implode(',' . PHP_EOL, $placeholders)),
            PHP_EOL,
        ]);

        return new Template($tpl, ...array_map(function ($factory) use ($container) {
            return $factory->compilable($container);
        }, $this->factories));
    }
}
