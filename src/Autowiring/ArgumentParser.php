<?php declare(strict_types=1);

namespace Quanta\Container\Autowiring;

use Quanta\Container\Alias;
use Quanta\Container\Parameter;
use Quanta\Container\Parsing\ParsedFactory;
use Quanta\Container\Parsing\ParsingFailure;
use Quanta\Container\Parsing\ParserInterface;
use Quanta\Container\Parsing\ParsingResultInterface;

final class ArgumentParser implements ArgumentParserInterface
{
    /**
     * The parser producing factories from option values.
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
    public function __invoke(\ReflectionParameter $parameter, array $options): ParsingResultInterface
    {
        if ($parameter->isVariadic()) {
            return new ParsingFailure;
        }

        $name = '$' . $parameter->getName();

        if (key_exists($name, $options)) {
            return ($this->parser)($options[$name]);
        }

        $type = $parameter->getType();

        if (! is_null($type) && ! $type->isBuiltIn()) {
            $class = $type->getName();

            return key_exists($class, $options)
                ? ($this->parser)($options[$class])
                : new ParsedFactory(
                    new Alias($class, $parameter->allowsNull())
                );
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
