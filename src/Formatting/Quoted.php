<?php declare(strict_types=1);

namespace Quanta\Container\Formatting;

final class Quoted
{
    /**
     * The string to quote.
     *
     * @var string
     */
    private $str;

    /**
     * Constructor.
     *
     * @param string $str
     */
    public function __construct(string $str)
    {
        $this->str = $str;
    }

    /**
     * Return the quoted string.
     *
     * @return string
     */
    public function __toString()
    {
        return '\'' . addslashes($this->str) . '\'';
    }
}
