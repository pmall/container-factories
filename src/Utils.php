<?php declare(strict_types=1);

namespace Quanta\Container;

final class Utils
{
    /**
     * Call the given method on all the objects of the given array with the
     * given extra arguments.
     *
     * @param object[]  $objects
     * @param string    $method
     * @param mixed     ...$xs
     * @return array
     */
    public static function plucked(array $objects, string $method, ...$xs): array
    {
        return array_map(function ($object) use ($method, $xs) {
            return $object->{$method}(...$xs);
        }, $objects);
    }

    /**
     * Return the given string with quotes.
     *
     * @param string $str
     * @return string
     */
    public static function quoted(string $str): string
    {
        return sprintf('\'%s\'', addslashes($str));
    }

    /**
     * Join an indent the given strings.
     *
     * @param string ...$strs
     * @return string
     */
    public static function indented(string ...$strs): string
    {
        $str = implode(',' . PHP_EOL, $strs);

        return trim($str) != ''
            ? (string) preg_replace('/^.+?$/m', '    $0', $str)
            : '';
    }

    /**
     * Return a string representation of the given array of strings.
     *
     * @param string[] $strs
     * @return string
     */
    public static function arrayStr(array $strs): string
    {
        if (count($strs) > 0) {
            $keys = array_keys($strs);
            $vals = array_values($strs);

            $strs = count($keys) > count(array_filter($keys, 'is_int'))
                ? array_map(function ($key, $val) {
                    return vsprintf('%s => %s', [
                        is_int($key) ? $key : sprintf('\'%s\'', addslashes($key)),
                        $val,
                    ]);
                }, $keys, $vals)
                : $vals;

            return vsprintf('[%s%s,%s]', [
                PHP_EOL,
                self::indented(...$strs),
                PHP_EOL,
            ]);
        }

        return '[]';
    }

    /**
     * Return a string representation of the callable made from the given class
     * name and static method name.
     *
     * @param string $class
     * @param string $method
     * @return string
     */
    public static function staticMethodStr(string $class, string $method): string
    {
        return sprintf('[\%s::class, \'%s\']', ltrim($class, '\\'), $method);
    }

    /**
     * Return a string representation of a self executed closure with the given
     * body.
     *
     * @param string $container
     * @param string $body
     * @return string
     */
    public static function selfExecutingClosureStr(string $container, string $body)
    {
        return vsprintf('(function (\%s $%s) {%s%s%s})($%s)', [
            \Psr\Container\ContainerInterface::class,
            $container,
            PHP_EOL,
            self::indented($body),
            PHP_EOL,
            $container,
        ]);
    }
}
