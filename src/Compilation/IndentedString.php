<?php declare(strict_types=1);

namespace Quanta\Container\Compilation;

final class IndentedString
{
    /**
     * The string to indent.
     *
     * @var string
     */
    private $str;

    /**
     * The number of spaces to preprend each lines with.
     *
     * @var int
     */
    private $nb;

    /**
     * Constructor.
     *
     * @param string    $str
     * @param int       $nb
     */
    public function __construct(string $str, int $nb = 4)
    {
        $this->str = $str;
        $this->nb = $nb;
    }

    /**
     * Return the indented string.
     *
     * @return string
     */
    public function __toString()
    {
        $spaces = str_pad('', $this->nb, ' ');

        return trim($this->str) != ''
            ? (string) preg_replace('/^.+?$/m', $spaces . '$0', $this->str)
            : '';
    }
}
