<?php declare(strict_types=1);

namespace Quanta\Container\Parsing;

interface ParserInterface
{
    /**
     * Return a parsed factory from the given value.
     *
     * @param mixed $value
     * @return \Quanta\Container\Parsing\ParsingResultInterface
     */
    public function __invoke($value): ParsingResultInterface;
}
