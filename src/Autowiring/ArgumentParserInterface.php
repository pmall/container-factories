<?php declare(strict_types=1);

namespace Quanta\Container\Autowiring;

use Quanta\Container\Parsing\ParsingResultInterface;

interface ArgumentParserInterface
{
    /**
     * Return a parsing result from the given reflection parameter and
     * autowiring options.
     *
     * The autowiring options parameter is an associative array of parameter
     * name (starting with '$') or class name to value pairs.
     *
     * @param \ReflectionParameter  $parameter
     * @param array                 $options
     * @return \Quanta\Container\Parsing\ParsingResultInterface
     */
    public function __invoke(\ReflectionParameter $parameter, array $options): ParsingResultInterface;
}
