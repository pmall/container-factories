<?php declare(strict_types=1);

namespace Quanta\Container\Factories;

final class Compiler
{
    /**
     * The closure compiler.
     *
     * @var \Quanta\Container\Factories\ClosureCompilerInterface
     */
    private $compiler;

    /**
     * Return a new Compiler with a dummy closure compiler (testing purpose).
     *
     * @return \Quanta\Container\Factories\Compiler
     */
    public static function withDummyClosureCompiler(): Compiler
    {
        return new Compiler(new DummyClosureCompiler);
    }

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Factories\ClosureCompilerInterface $compiler
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
     * @throws \LogicException
     */
    public function __invoke(callable $factory): string
    {
        if (is_object($factory)) {
            if ($factory instanceof CompilableFactoryInterface) {
                return (string) $factory->compiled($this);
            }

            if ($factory instanceof \Closure) {
                return ($this->compiler)($factory);
            }

            throw new \LogicException(
                sprintf('Unable to compile instance of %s', get_class($factory))
            );
        }

        if (is_array($factory)) {
            if (is_string($factory[0])) {
                return vsprintf('[\%s::class, \'%s\']', [
                    ltrim($factory[0], '\\'),
                    $factory[1]
                ]);
            }

            throw new \LogicException('Unable to compile a non static method call');
        }

        return strval($factory);
    }
}
