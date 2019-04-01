<?php declare(strict_types=1);

namespace Quanta\Container\Parsing;

interface ParserInterface
{
    /**
     * Return a parsed factory from the given value.
     *
     * @param mixed $value
     * @return \Quanta\Container\Parsing\ParsedFactoryInterface
     */
    public function __invoke($value): ParsedFactoryInterface;
}
