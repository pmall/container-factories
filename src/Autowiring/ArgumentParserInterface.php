<?php declare(strict_types=1);

namespace Quanta\Container\Autowiring;

use Quanta\Container\Parsing\ParsingResultInterface;

interface ArgumentParserInterface
{
    /**
     * Return a parsing result from the given reflection parameter.
     *
     * @param \ReflectionParameter $parameter
     * @return \Quanta\Container\Parsing\ParsingResultInterface
     */
    public function __invoke(\ReflectionParameter $parameter): ParsingResultInterface;
}
