<?php declare(strict_types=1);

namespace Quanta\Container\Compilation;

final class CompiledString implements CompilableInterface
{
    /**
     * The already compiled string.
     *
     * @var string
     */
    private $str;

    /**
     * Constructor.
     *
     * @param string $str
     */
    public function __construct($str)
    {
        $this->str = $str;
    }

    /**
     * @inheritdoc
     */
    public function compiled(Compiler $compiler): string
    {
        return $this->str;
    }
}
