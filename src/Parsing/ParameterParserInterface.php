<?php declare(strict_types=1);

namespace Quanta\Container\Parsing;

interface ParameterParserInterface
{
    /**
     * Return a parsed factory from the given reflection parameter and
     * autowiring options.
     *
     * The autowiring options parameter is an associative array of parameter
     * name (starting with '$') or class name to value pairs.
     *
     * @param \ReflectionParameter  $parameter
     * @param array                 $options
     * @return \Quanta\Container\Parsing\ParsedFactoryInterface
     */
    public function __invoke(\ReflectionParameter $parameter, array $options): ParsedFactoryInterface;
}
