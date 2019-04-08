<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Autowiring\AutowiredInstance;
use Quanta\Container\Autowiring\ArgumentParserInterface;

final class AutowiredFactoryMap implements FactoryMapInterface
{
    /**
     * The argument parser used to parse the class constructor parameters.
     *
     * @var \Quanta\Container\Autowiring\ArgumentParserInterface
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
     * @param \Quanta\Container\Autowiring\ArgumentParserInterface  $parser
     * @param array[]                                               $map
     * @throws \InvalidArgumentException
     */
    public function __construct(ArgumentParserInterface $parser, array $map)
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
        $classes = array_keys($this->map);

        return (array) array_combine($classes, array_map(function ($class, $options) {
            return new DefinitionProxy(
                new AutowiredInstance($this->parser, $class, $options)
            );
        }, $classes, $this->map));
    }
}
