<?php declare(strict_types=1);

namespace Quanta\Container\Parsing;

use Quanta\Container\Alias;
use Quanta\Container\Parameter;

final class ParameterParser implements ParameterParserInterface
{
    /**
     * @inheritdoc
     */
    public function __invoke(\ReflectionParameter $parameter): ParsedFactoryInterface
    {
        if ($parameter->isVariadic()) {
            return new ParsingFailure;
        }

        $type = $parameter->getType();

        if (! is_null($type) && ! $type->isBuiltIn()) {
            return new ParsedFactory(
                new Alias($type->getName(), $parameter->allowsNull())
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
