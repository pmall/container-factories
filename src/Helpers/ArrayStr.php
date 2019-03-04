<?php declare(strict_types=1);

namespace Quanta\Container\Helpers;

final class ArrayStr
{
    /**
     * The array of strings to represent as a string.
     *
     * @var string[]
     */
    private $strs;

    /**
     * Constructor.
     *
     * @param string[] $strs
     */
    public function __construct(array $strs)
    {
        $result = \Quanta\ArrayTypeCheck::result($strs, 'string');

        if (! $result->isValid()) {
            throw new \InvalidArgumentException(
                $result->message()->constructor($this, 1)
            );
        }

        $this->strs = $strs;
    }

    /**
     * Return a string representation of the array.
     *
     * @return string
     */
    public function __toString()
    {
        if (count($this->strs) > 0) {
            $keys = array_keys($this->strs);
            $vals = array_values($this->strs);

            $strs = count($keys) > count(array_filter($keys, 'is_int'))
                ? array_map([$this, 'pairStr'], $keys, $vals)
                : $vals;

            return vsprintf('[%s%s,%s]', [
                PHP_EOL,
                new IndentedStr((string) new LinesStr(...$strs)),
                PHP_EOL,
            ]);
        }

        return '[]';
    }

    /**
     * Return a string representation of the given key => value pairs.
     *
     * @param int|string    $key
     * @param string        $val
     * @return string
     */
    private function pairStr($key, string $val): string
    {
        $key = is_int($key) ? $key : sprintf('\'%s\'', addslashes($key));

        return sprintf('%s => %s', $key, $val);
    }
}
