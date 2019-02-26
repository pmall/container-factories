<?php declare(strict_types=1);

namespace Quanta\Container\Helpers;

final class LinesStr
{
    /**
     * The array of lines to represent as a string.
     *
     * @var string[]
     */
    private $lines;

    /**
     * Constructor.
     *
     * @param string ...$lines
     */
    public function __construct(string ...$lines)
    {
        $this->lines = $lines;
    }

    /**
     * Return the lines spaced by a comma and a new line.
     *
     * @return string
     */
    public function __toString()
    {
        return implode(',' . PHP_EOL, $this->lines);
    }
}
