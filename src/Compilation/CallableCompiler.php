<?php declare(strict_types=1);

namespace Quanta\Container\Compilation;

use Quanta\Container\Factories\CompilableFactoryInterface;

final class CallableCompiler
{
    /**
     * The closure compiler.
     *
     * @var \Quanta\Container\Compilation\ClosureCompilerInterface
     */
    private $compiler;

    /**
     * Return a new CallableCompiler with a dummy closure compiler (testing
     * purpose).
     *
     * @return \Quanta\Container\Compilation\CallableCompiler
     */
    public static function withDummyClosureCompiler(): CallableCompiler
    {
        return new CallableCompiler(
            new DummyClosureCompiler
        );
    }

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
     * Return a string representation of the given callable.
     *
     * @param callable $callable
     * @return string
     * @throws \LogicException
     */
    public function compiled(callable $callable): string
    {
        if (is_object($callable)) {
            if ($callable instanceof CompilableFactoryInterface) {
                return $callable->compiled(new Template($this));
            }

            if ($callable instanceof \Closure) {
                return $this->compiler->compiled($callable);
            }

            throw new \LogicException(
                sprintf('Unable to compile instance of %s', get_class($callable))
            );
        }

        if (is_array($callable)) {
            if (is_string($callable[0])) {
                return vsprintf('[\%s::class, \'%s\']', [
                    ltrim($callable[0], '\\'),
                    $callable[1]
                ]);
            }

            throw new \LogicException('Unable to compile a non static method call');
        }

        return strval($callable);
    }
}
