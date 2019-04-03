<?php declare(strict_types=1);

namespace Quanta\Container\Parsing;

use Quanta\Container\Parameter;

final class FallbackParser implements ParserInterface
{
    /**
     * The parser.
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
    public function __invoke($value): ParsingResultInterface
    {
        $result = ($this->parser)($value);

        return ! $result->isParsed()
            ? new ParsedFactory(new Parameter($value))
            : $result;
    }
}
