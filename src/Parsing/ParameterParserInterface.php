<?php declare(strict_types=1);

namespace Quanta\Container\Parsing;

interface ParameterParserInterface
{
    /**
     * Return a parsed factory from the given reflection parameter and
     * autowiring options.
     *
     * @param \ReflectionParameter $parameter
     * @return \Quanta\Container\Parsing\ParsedFactoryInterface
     */
    public function __invoke(\ReflectionParameter $parameter): ParsedFactoryInterface;
}
