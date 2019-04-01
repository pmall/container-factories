<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\ParameterFactoryMap;
use Quanta\Container\Parsing\ParserInterface;

final class ParameterArray implements ConfigurationInterface
{
    /**
     * The parser used to produce factories from parameters.
     *
     * @var \Quanta\Container\Parsing\ParserInterface
     */
    private $parser;

    /**
     * The array of parameters to provide.
     *
     * @var array
     */
    private $parameters;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Parsing\ParserInterface $parser
     * @param array                                     $parameters
     */
    public function __construct(ParserInterface $parser, array $parameters)
    {
        $this->parser = $parser;
        $this->parameters = $parameters;
    }

    /**
     * @inheritdoc
     */
    public function unit(): ConfigurationUnitInterface
    {
        return new ConfigurationUnit(
            new ParameterFactoryMap($this->parser, $this->parameters)
        );
    }
}
