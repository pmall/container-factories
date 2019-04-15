<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Parsing\StringParserInterface;

final class ValueParser
{
    /**
     * The delegate parsers.
     *
     * @var \Quanta\Container\Parsing\StringParserInterface[]
     */
    private $parsers;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Parsing\StringParserInterface ...$parsers
     */
    public function __construct(StringParserInterface ...$parsers)
    {
        $this->parsers = $parsers;
    }

    /**
     * @inheritdoc
     */
    public function __invoke($value): FactoryInterface
    {
        if (is_array($value) && ! is_callable($value)) {
            return new FactoryArray(array_map($this, $value));
        }

        if (is_string($value)) {
            foreach ($this->parsers as $parser) {
                $result = $parser($value);

                if ($result->isParsed()) {
                    return $result->factory();
                }
            }
        }

        return new Parameter($value);
    }
}
