<?php declare(strict_types=1);

namespace Quanta\Container;

final class ParsedFactoryMap implements FactoryMapInterface
{
    /**
     * The parser used to produce factories from the values.
     *
     * @var \Quanta\Container\ValueParser
     */
    private $parser;

    /**
     * The array of values.
     *
     * @var array
     */
    private $values;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\ValueParser $parser
     * @param array                         $values
     */
    public function __construct(ValueParser $parser, array $values)
    {
        $this->parser = $parser;
        $this->values = $values;
    }

    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        return array_map($this->parser, $this->values);
    }
}
