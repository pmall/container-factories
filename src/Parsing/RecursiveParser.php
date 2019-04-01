<?php declare(strict_types=1);

namespace Quanta\Container\Parsing;

final class RecursiveParser implements ParserInterface
{
    /**
     * The parser to use recursively on array values.
     *
     * @var \Quanta\Container\Parsing\ParserInterface
     */
    private $parser;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Parsing\ParserInterface $parser
     */
    public function __construct(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @inheritdoc
     */
    public function __invoke($value): ParsedFactoryInterface
    {
        if (is_array($value)) {
            return new ParsedFactoryArray(array_map($this->parser, $value));
        }

        return ($this->parser)($value);
    }
}
