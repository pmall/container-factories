<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Parsing\ParameterParserInterface;

final class AutowiredFactoryMap implements FactoryMapInterface
{
    /**
     * The parser used to produce factories from reflection parameters.
     *
     * @var \Quanta\Container\Parsing\ParameterParserInterface
     */
    private $parser;

    /**
     * The class name to autowiring option map.
     *
     * @var array[]
     */
    private $map;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Parsing\ParameterParserInterface    $parser
     * @param array[]                                               $map
     * @throws \InvalidArgumentException
     */
    public function __construct(ParameterParserInterface $parser, array $map)
    {
        $result = \Quanta\ArrayTypeCheck::result($map, 'array');

        if (! $result->isValid()) {
            throw new \InvalidArgumentException(
                $result->message()->constructor($this, 2)
            );
        }

        $this->parser = $parser;
        $this->map = $map;
    }

    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        $factories = [];

        foreach ($this->map as $class => $options) {
            $factories[$class] = new DefinitionProxy(
                new AutowiredInstance($this->parser, $class, $options)
            );
        }

        return $factories;
    }
}
