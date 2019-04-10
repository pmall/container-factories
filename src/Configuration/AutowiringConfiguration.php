<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\AutowiredFactoryMap;
use Quanta\Container\Autowiring\ArgumentParserInterface;

final class AutowiringConfiguration implements ConfigurationInterface
{
    /**
     * The argument parser used to parse the class constructor parameters.
     *
     * @var \Quanta\Container\Autowiring\ArgumentParserInterface
     */
    private $parser;

    /**
     * The collection of class name to autowire.
     *
     * @var iterable
     */
    private $classes;

    /**
     * The associative array of pattern to autowiring options.
     *
     * @var array[]
     */
    private $options;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Autowiring\ArgumentParserInterface  $parser
     * @param iterable                                              $classes
     * @param array[]                                               $options
     * @throws \InvalidArgumentException
     */
    public function __construct(ArgumentParserInterface $parser, iterable $classes, array $options = [])
    {
        $result = \Quanta\ArrayTypeCheck::result($options, 'array');

        if (! $result->isValid()) {
            throw new \InvalidArgumentException(
                $result->message()->constructor($this, 3)
            );
        }

        $this->parser = $parser;
        $this->classes = $classes;
        $this->options = $options;
    }

    public function unit(): ConfigurationUnitInterface
    {
        $map = [];

        foreach ($this->classes as $class) {
            if (is_string($class)) {
                $map[$class] = [];
            }
        }

        $classes = array_keys($map);

        uksort($this->options, function ($a, $b) {
            return strlen((string) $a) - strlen((string) $b);
        });

        foreach ($this->options as $pattern => $options) {
            $pattern = '/' . str_replace('\\*', '.+?', preg_quote($pattern)) . '/';

            foreach (preg_grep($pattern, $classes) as $matched) {
                $map[$matched] = $options + $map[$matched];
            }
        }

        return new ConfigurationUnit(
            new AutowiredFactoryMap($this->parser, $map)
        );
    }
}
