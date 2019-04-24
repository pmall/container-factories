<?php declare(strict_types=1);

namespace Quanta\Container\Parsing;

final class CompositeParameterParser implements ParameterParserInterface
{
    /**
     * The array of parameter parsers.
     *
     * @var \Quanta\Container\Parsing\ParameterParserInterface[] $parsers
     */
    private $parsers;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Parsing\ParameterParserInterface ...$parsers
     */
    public function __construct(ParameterParserInterface ...$parsers)
    {
        $this->parsers = $parsers;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(\ReflectionParameter $parameter): ParsedFactoryInterface
    {
        foreach ($this->parsers as $parser) {
            $result = $parser($parameter);

            if ($result->isParsed()) {
                return $result;
            }
        }

        return new ParsingFailure;
    }
}
