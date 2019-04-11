<?php declare(strict_types=1);

namespace Quanta\Container\Compilation;

use Psr\Container\ContainerInterface;

use Quanta\Container\FactoryInterface;

final class Compiler
{
    /**
     * The closure compiler.
     *
     * @var \Quanta\Container\Compilation\ClosureCompilerInterface
     */
    private $compiler;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Compilation\ClosureCompilerInterface $compiler
     */
    public function __construct(ClosureCompilerInterface $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * Return a string representation of the given factory.
     *
     * @param callable $factory
     * @return string
     * @throws \InvalidArgumentException
     */
    public function __invoke(callable $factory): string
    {
        if (is_array($factory)) {
            if (is_string($factory[0])) {
                return vsprintf('[%s::class, \'%s\']', $factory);
            }

            throw new \InvalidArgumentException(
                (string) new ObjectCompilationErrorMessage($factory[0])
            );
        }

        if (is_object($factory)) {
            if ($factory instanceof FactoryInterface) {
                return implode(PHP_EOL, [
                    sprintf('function (%s $container) {', ContainerInterface::class),
                    new IndentedString('return ' . $factory->compiled('container', $this) . ';'),
                    '}',
                ]);
            }

            if ($factory instanceof \Closure) {
                return ($this->compiler)($factory);
            }

            throw new \InvalidArgumentException(
                (string) new ObjectCompilationErrorMessage($factory)
            );
        }

        return '\'' . strval($factory) . '\'';
    }
}
