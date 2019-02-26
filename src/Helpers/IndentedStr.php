<?php declare(strict_types=1);

namespace Quanta\Container\Helpers;

final class IndentedStr
{
    /**
     * The string to indent.
     *
     * @var string
     */
    private $str;

    /**
     * The number of spaces the lines must be prepended with.
     *
     * @var int
     */
    private $indent;

    /**
     * Constructor.
     *
     * @param string    $str
     * @param int       $indent
     */
    public function __construct(string $str, int $indent = 4)
    {
        $this->str = $str;
        $this->indent = $indent;
    }

    /**
     * Return the string with all the lines prepended with the number of spaces.
     *
     * @return string
     */
    public function __toString(): string
    {
        if (trim($this->str) != '') {
            $spaces = str_pad('', $this->indent, ' ') . '$0';

            return (string) preg_replace('/^.+?$/m', $spaces, $this->str);
        }

        return '';
    }
}
