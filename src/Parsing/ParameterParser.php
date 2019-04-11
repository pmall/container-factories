<?php declare(strict_types=1);

namespace Quanta\Container\Parsing;

use Quanta\Container\Alias;
use Quanta\Container\Parameter;
use Quanta\Container\ValueParser;

final class ParameterParser implements ParameterParserInterface
{
    /**
     * The parser producing factories from option values.
     *
     * @var \Quanta\Container\ValueParser
     */
    private $parser;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\ValueParser $parser
     */
    public function __construct(ValueParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(\ReflectionParameter $parameter, array $options): ParsedFactoryInterface
    {
        if ($parameter->isVariadic()) {
            return new ParsingFailure;
        }

        $name = '$' . $parameter->getName();

        if (key_exists($name, $options)) {
            return new ParsedFactory(($this->parser)($options[$name]));
        }

        $type = $parameter->getType();

        if (! is_null($type) && ! $type->isBuiltIn()) {
            $class = $type->getName();

            return key_exists($class, $options)
                ? new ParsedFactory(new Alias($options[$class], false))
                : new ParsedFactory(new Alias($class, $parameter->allowsNull()));
        }

        if ($parameter->isDefaultValueAvailable()) {
            return new ParsedFactory(
                new Parameter($parameter->getDefaultValue())
            );
        }

        if ($parameter->allowsNull()) {
            return new ParsedFactory(
                new Parameter(null)
            );
        }

        return new ParsingFailure;
    }
}
