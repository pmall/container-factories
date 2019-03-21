<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Maps\FactoryMapInterface;
use Quanta\Container\Passes\MergedProcessingPass;
use Quanta\Container\Passes\ProcessingPassInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * The factory map.
     *
     * @var \Quanta\Container\Maps\FactoryMapInterface
     */
    private $map;

    /**
     * The array of processing passes.
     *
     * @var \Quanta\Container\Passes\ProcessingPassInterface[]
     */
    private $passes;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Maps\FactoryMapInterface        $map
     * @param \Quanta\Container\Passes\ProcessingPassInterface  ...$passes
     */
    public function __construct(FactoryMapInterface $map, ProcessingPassInterface ...$passes)
    {
        $this->map = $map;
        $this->passes = $passes;
    }

    /**
     * @inheritdoc
     */
    public function entry(): ConfigurationEntry
    {
        return new ConfigurationEntry($this->map,
            new MergedProcessingPass(...$this->passes)
        );
    }
}
