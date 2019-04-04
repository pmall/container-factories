<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Parsing\ParserInterface;

final class AutowiredFactoryMap
{
    private $parser;

    private $map;

    public function __construct(ParserInterface $parser, array $map)
    {
        $this->parser = $parser;
        $this->map = $map;
    }

    public function factories(): array
    {
        return array_map(function ($class, $options) {
            return new DefinedFactory(
                new AutowiredInstance(
                    new ArgumentParser($this->parser, $options),
                    $class
                )
            );
        }, array_keys($this->map), $this->map);
    }
}
