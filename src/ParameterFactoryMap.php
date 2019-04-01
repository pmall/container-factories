<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Parsing\ParserInterface;

final class ParameterFactoryMap implements FactoryMapInterface
{
    /**
     * The parser used to produce factories from the values.
     *
     * @var \Quanta\Container\Parsing\ParserInterface
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
     * @param \Quanta\Container\Parsing\ParserInterface $parser
     * @param array                                     $values
     */
    public function __construct(ParserInterface $parser, array $values)
    {
        $this->parser = $parser;
        $this->values = $values;
    }

    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        $parsed = array_map($this->parser, $this->values);

        return array_map(function ($parsed) {
            return $parsed->factory();
        }, $parsed);
    }
}
