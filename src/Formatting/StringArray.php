<?php declare(strict_types=1);

namespace Quanta\Container\Formatting;

final class StringArray
{
    /**
     * The strings to display as an array.
     *
     * @var string[]
     */
    private $strs;

    /**
     * Construct.
     *
     * @param string[] $strs
     */
    public function __construct(array $strs)
    {
        $this->strs = $strs;
    }

    /**
     * Return the array.
     *
     * @return string
     */
    public function __toString()
    {
        if (count($this->strs) == 0) {
            return '[]';
        }

        $keys = array_keys($this->strs);

        if ($keys === range(0, count($this->strs) - 1)) {
            return implode(PHP_EOL, [
                '[',
                new IndentedString(implode(',' . PHP_EOL, $this->strs)) . ',',
                ']',
            ]);
        }

        return implode(PHP_EOL, [
            '[',
            new IndentedString(implode(PHP_EOL, array_map(function ($key, $str) {
                return vsprintf('%s => %s', [
                    is_int($key) ? $key : (string) new Quoted($key),
                    $str
                ]) . ',';
            }, array_keys($this->strs), $this->strs))),
            ']',
        ]);
    }
}
