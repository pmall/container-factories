<?php declare(strict_types=1);

namespace Quanta\Container\Factories;

use Quanta\Container\Utils;

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
        return new self(new DummyClosureCompiler);
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
                $this->compilationErrorMessage($factory, '__invoke')
            );
        }

        if (is_array($factory)) {
            if (is_string($factory[0])) {
                return Utils::staticMethodStr(...$factory);
            }

            throw new \LogicException(
                $this->compilationErrorMessage(...$factory)
            );
        }

        return strval($factory);
    }

    /**
     * Return the message of the exception thrown when a factory is not
     * compilable.
     *
     * @param object $object
     * @param string $method
     * @return string
     */
    private function compilationErrorMessage($object, string $method): string
    {
        $tpl = implode(' ', [
            'Unable to compile function %s::%s() - only',
            'implementations of %s,',
            'closures,',
            'static method arrays',
            'and function names can be compiled',
        ]);

        return vsprintf($tpl, [
            get_class($object),
            $method,
            CompilableFactoryInterface::class,
        ]);
    }
}
