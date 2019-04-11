<?php declare(strict_types=1);

namespace Quanta\Container\Parsing;

use Quanta\Container\EnvVar;

final class EnvVarParser implements StringParserInterface
{
    /**
     * @inheritdoc
     */
    public function __invoke(string $value): ParsedFactoryInterface
    {
        if (preg_match('/^env\((.+?)\)$/', $value, $matches)) {
            $xs = explode(',', $matches[1]);
            $xs = array_map('trim', $xs);
            $xs = array_filter($xs);

            $nb = count($xs);

            if ($nb > 0 && $nb < 4) {
                return new ParsedFactory(new EnvVar(...$xs));
            }
        }

        return new ParsingFailure;
    }
}
