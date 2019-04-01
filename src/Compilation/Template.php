<?php declare(strict_types=1);

namespace Quanta\Container\Compilation;

final class Template implements CompilableInterface
{
    /**
     * The sprintf format.
     *
     * @var string
     */
    private $format;

    /**
     * The values to compile and to use as sprintf argument.
     *
     * @var array $xs
     */
    private $xs;

    /**
     * Constructor.
     *
     * @param string    $format
     * @param mixed     ...$xs
     */
    public function __construct(string $format, ...$xs)
    {
        $this->format = $format;
        $this->xs = $xs;
    }

    /**
     * @inheritdoc
     */
    public function compiled(Compiler $compiler): string
    {
        return vsprintf($this->format, array_map(function ($x) use ($compiler) {
            return $compiler($x);
        }, $this->xs));
    }
}
