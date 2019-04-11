<?php declare(strict_types=1);

namespace Quanta\Container\Parsing;

interface StringParserInterface
{
    /**
     * Return a parsed factory from the given string.
     *
     * @param string $value
     * @return \Quanta\Container\Parsing\ParsedFactoryInterface
     */
    public function __invoke(string $value): ParsedFactoryInterface;
}
