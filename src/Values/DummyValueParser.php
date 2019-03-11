<?php declare(strict_types=1);

namespace Quanta\Container\Values;

final class DummyValueParser implements ValueParserInterface
{
    /**
     * The map associating a value to its parsed value.
     *
     * @var array
     */
    private $map;

    /**
     * Constructor.
     *
     * @param array $map
     */
    public function __construct(array $map)
    {
        $this->map = $map;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(ValueFactory $factory, $value): ParsedValueInterface
    {
        if (is_string($value) && array_key_exists($value, $this->map)) {
            return new ParsedValue(new Value($this->map[$value]));
        }

        return new ParsingFailure('The value is not in the dummy parser map');
    }
}
