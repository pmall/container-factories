<?php declare(strict_types=1);

namespace Quanta\Container\Values;

final class InterpolatedStringParser implements ValueParserInterface
{
    /**
     * @inheritdoc
     */
    public function __invoke(ValueFactory $factory, $value): ParsedValueInterface
    {
        if (is_string($value)) {
            if (preg_match_all('/%\{(.+?)\}/', $value, $matches)) {
                $format = (string) preg_replace('/%\{(.+?)\}/', '%s', $value);

                return new ParsedValue(new InterpolatedString($format, ...$matches[1]));
            }

            return new ParsingFailure(
                sprintf('String \'%s\' is not formatted as an interpolated string', $value)
            );
        }

        return new ParsingFailure('Only strings can be parsed as an interpolated string');
    }
}
