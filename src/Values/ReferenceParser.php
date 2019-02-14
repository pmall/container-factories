<?php declare(strict_types=1);

namespace Quanta\Container\Values;

final class ReferenceParser implements ValueParserInterface
{
    /**
     * @inheritdoc
     */
    public function __invoke(ValueFactory $factory, $value): ParsedValueInterface
    {
        if (is_string($value)) {
            if (preg_match('/^@(.+?)$/', $value, $matches)) {
                return new ParsedValue(new Reference($matches[1], false));
            }

            return new ParsingFailure(
                sprintf('String \'%s\' is not formatted as a reference', $value)
            );
        }

        return new ParsingFailure('Only strings can be parsed as a reference');
    }
}
