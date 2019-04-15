<?php declare(strict_types=1);

namespace Quanta\Container\Formatting;

final class StringArgList
{
    /**
     * The strings to display as an argument list.
     *
     * @var string[]
     */
    private $strs;

    /**
     * Constructor.
     *
     * @param string[] ...$strs
     */
    public function __construct(string ...$strs)
    {
        $this->strs = $strs;
    }

    /**
     * Return the argument list.
     *
     * @return string
     */
    public function __toString()
    {
        if (count($this->strs) == 0) {
            return '()';
        }

        return implode(PHP_EOL, [
            '(',
            new IndentedString(implode(',' . PHP_EOL, $this->strs)),
            ')',
        ]);
    }
}
