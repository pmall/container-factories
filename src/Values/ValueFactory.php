<?php declare(strict_types=1);

namespace Quanta\Container\Values;

final class ValueFactory
{
    /**
     * The parsers trying to parse a value as a ValueInterface implementation.
     *
     * @var \Quanta\Container\Values\ValueParserInterface[]
     */
    private $parsers;

    /**
     * Return a new ValueFactory with a dummy value parser using the given map.
     *
     * @param array $map
     * @return \Quanta\Container\Values\ValueFactory
     */
    public static function withDummyValueParser(array $map): ValueFactory
    {
        return new ValueFactory(
            new DummyParser($map)
        );
    }

    /**
     * Return a new ValueFactory with the default value parsers.
     *
     * @return \Quanta\Container\Values\ValueFactory
     */
    public static function withDefaultValueParser(): ValueFactory
    {
        return new ValueFactory(...[
            new EnvVarParser,
            new InstanceParser,
            new ReferenceParser,
            new InterpolatedStringParser,
        ]);
    }

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Values\ValueParserInterface ...$parsers
     */
    public function __construct(ValueParserInterface ...$parsers)
    {
        $this->parsers = $parsers;
    }

    /**
     * @inheritdoc
     */
    public function __invoke($value): ValueInterface
    {
        foreach ($this->parsers as $parser) {
            $result = $parser($this, $value);

            if ($result->success()) {
                return $result->value();
            }
        }

        if (is_array($value)) {
            return new ArrayValue(array_map($this, $value));
        }

        return new Value($value);
    }
}
