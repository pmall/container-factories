<?php declare(strict_types=1);

namespace Quanta\Container\Parsing;

use Quanta\Container\Alias;

final class AliasParser implements ParserInterface
{
    /**
     * @inheritdoc
     */
    public function __invoke($value): ParsingResultInterface
    {
        if (is_string($value)) {
            if (preg_match('/^@(.+?)$/', $value, $matches)) {
                return new ParsedFactory(
                    new Alias($matches[1])
                );
            }

            return new ParsingFailure;
        }

        return new ParsingFailure;
    }
}
