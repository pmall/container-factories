<?php declare(strict_types=1);

namespace Quanta\Container\Parsing;

use Quanta\Container\Alias;

final class AliasParser implements StringParserInterface
{
    /**
     * @inheritdoc
     */
    public function __invoke(string $value): ParsedFactoryInterface
    {
        if (preg_match('/^@(.+?)$/', $value, $matches)) {
            return new ParsedFactory(new Alias($matches[1]));
        }

        return new ParsingFailure;
    }
}
