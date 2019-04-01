<?php declare(strict_types=1);

namespace Quanta\Container\Compilation;

final class Compilable implements CompilableInterface
{
    /**
     * The value to compile.
     *
     * @var mixed
     */
    private $value;

    /**
     * Constructor.
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function compiled(Compiler $compiler): string
    {
        return $compiler($this->value);
    }
}
