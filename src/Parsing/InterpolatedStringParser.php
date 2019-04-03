<?php declare(strict_types=1);

namespace Quanta\Container\Parsing;

use Quanta\Container\InterpolatedString;

final class InterpolatedStringParser implements ParserInterface
{
    /**
     * @inheritdoc
     */
    public function __invoke($value): ParsingResultInterface
    {
        if (is_string($value)) {
            if (preg_match_all('/%\{(.+?)\}/', $value, $matches)) {
                $format = (string) preg_replace('/%\{(.+?)\}/', '%s', $value);

                return new ParsedFactory(
                    new InterpolatedString($format, ...$matches[1])
                );
            }

            return new ParsingFailure;
        }

        return new ParsingFailure;
    }
}
