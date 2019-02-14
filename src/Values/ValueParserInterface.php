<?php declare(strict_types=1);

namespace Quanta\Container\Values;

interface ValueParserInterface
{
    /**
     * Return a parsed value from the given value.
     *
     * A value factory is given so values can be parsed recursively.
     *
     * @param \Quanta\Container\Values\ValueFactory             $factory
     * @param mixed                                             $value
     * @return \Quanta\Container\Values\ParsedValueInterface
     * @throws \LogicException
     */
    public function __invoke(ValueFactory $factory, $value): ParsedValueInterface;
}
