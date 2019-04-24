<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\DefinitionProxy;
use Quanta\Container\AutowiredInstance;
use Quanta\Container\Parsing\ParameterParser;
use Quanta\Container\Parsing\ParameterParserInterface;
use Quanta\Container\Parsing\CompositeParameterParser;

final class AutowiringConfiguration implements ConfigurationInterface
{
    /**
     * The collection of class name to autowire.
     *
     * @var iterable
     */
    private $classes;

    /**
     * The associative array of pattern to parameter parser.
     *
     * @var \Quanta\Container\Parsing\ParameterParserInterface[]
     */
    private $options;

    /**
     * Constructor.
     *
     * @param iterable                                              $classes
     * @param \Quanta\Container\Parsing\ParameterParserInterface[]  $options
     * @throws \InvalidArgumentException
     */
    public function __construct(iterable $classes, array $options = [])
    {
        $result = \Quanta\ArrayTypeCheck::result($options, ParameterParserInterface::class);

        if (! $result->isValid()) {
            throw new \InvalidArgumentException(
                $result->message()->constructor($this, 2)
            );
        }

        $this->classes = $classes;
        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        $map = [];

        foreach ($this->classes as $class) {
            if (is_string($class)) {
                $map[$class] = [new ParameterParser];
            }
        }

        $classes = array_keys($map);

        uksort($this->options, function ($a, $b) {
            return strlen((string) $a) - strlen((string) $b);
        });

        foreach ($this->options as $pattern => $parser) {
            $pattern = '/' . str_replace('\\*', '.+?', preg_quote($pattern)) . '/';

            foreach (preg_grep($pattern, $classes) as $matched) {
                array_unshift($map[$matched], $parser);
            }
        }

        $factories = [];

        foreach ($map as $class => $parsers) {
            $factories[$class] = new DefinitionProxy(
                new AutowiredInstance($class, count($parsers) > 1
                    ? new CompositeParameterParser(...$parsers)
                    : $parsers[0]
                )
            );
        }

        return $factories;
    }

    /**
     * @inheritdoc
     */
    public function mappers(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function extensions(): array
    {
        return [];
    }
}
