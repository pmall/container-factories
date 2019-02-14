<?php declare(strict_types=1);

namespace Quanta\Container\Values;

final class EnvVarParser implements ValueParserInterface
{
    /**
     * @inheritdoc
     */
    public function __invoke(ValueFactory $factory, $value): ParsedValueInterface
    {
        if (is_string($value)) {
            if (preg_match('/^env\((.+?)\)$/', $value, $matches)) {
                $xs = explode(',', $matches[1]);
                $xs = array_map('trim', $xs);
                $xs = array_filter($xs);

                $nb = count($xs);

                if ($nb > 0 && $nb < 4) {
                    return new ParsedValue(new EnvVar(...$xs));
                }
            }

            return new ParsingFailure(
                sprintf('String \'%s\' is not formatted as an env variable', $value)
            );
        }

        return new ParsingFailure('Only strings can be parsed as an env variable');
    }
}
