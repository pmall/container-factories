<?php declare(strict_types=1);

namespace Quanta\Container\Parsing;

final class CompositeParser implements ParserInterface
{
    /**
     * The array of parsers.
     *
     * @var \Quanta\Container\Parsing\ParserInterface[]
     */
    private $parsers;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Parsing\ParserInterface ...$parsers
     */
    public function __construct(ParserInterface ...$parsers)
    {
        $this->parsers = $parsers;
    }

    /**
     * @inheritdoc
     */
    public function __invoke($value): ParsedFactoryInterface
    {
        foreach ($this->parsers as $parser) {
            $parsed = $parser($value);

            if ($parsed->success()) {
                return $parsed;
            }
        }

        return new ParsingFailure($value);
    }
}
