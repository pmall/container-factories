<?php declare(strict_types=1);

namespace Quanta\Container\Values;

final class InstanceParser implements ValueParserInterface
{
    /**
     * @inheritdoc
     */
    public function __invoke(ValueFactory $factory, $value): ParsedValueInterface
    {
        if (is_array($value)) {
            if (count($value) > 1) {
                if (count(array_filter($value, 'is_string', ARRAY_FILTER_USE_KEY)) == 0) {
                    if ($value[0] == 'new' && is_string($value[1])) {
                        array_shift($value);
                        $class = array_shift($value);
                        $values = array_map($factory, $value);

                        return new ParsedValue(new Instance($class, ...$values));
                    }
                }
            }

            return new ParsingFailure('The array is not formatted as an instance');
        }

        return new ParsingFailure('Only arrays can be parsed as an instance');
    }
}
