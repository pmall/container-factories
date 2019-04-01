<?php declare(strict_types=1);

namespace Quanta\Container\Compilation;

final class DummyClosureCompiler implements ClosureCompilerInterface
{
    /**
     * @inheritdoc
     */
    public function __invoke(\Closure $closure): string
    {
        throw new \LogicException;
    }
}
