<?php declare(strict_types=1);

namespace Quanta\Container\Parsing;

use Quanta\Container\EnvVar;

final class EnvVarParser implements ParserInterface
{
    /**
     * @inheritdoc
     */
    public function __invoke($value): ParsedFactoryInterface
    {
        if (is_string($value)) {
            if (preg_match('/^env\((.+?)\)$/', $value, $matches)) {
                $xs = explode(',', $matches[1]);
                $xs = array_map('trim', $xs);
                $xs = array_filter($xs);

                $nb = count($xs);

                if ($nb > 0 && $nb < 4) {
                    return new ParsedFactory(new EnvVar(...$xs));
                }
            }

            return new ParsingFailure($value);
        }

        return new ParsingFailure($value);
    }
}
