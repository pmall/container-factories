<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\FactoryMapInterface;
use Quanta\Container\Configuration\Passes\MergedProcessingPass;
use Quanta\Container\Configuration\Passes\ProcessingPassInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * The factory map.
     *
     * @var \Quanta\Container\FactoryMapInterface
     */
    private $map;

    /**
     * The array of processing passes.
     *
     * @var \Quanta\Container\Configuration\Passes\ProcessingPassInterface[]
     */
    private $passes;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\FactoryMapInterface                             $map
     * @param \Quanta\Container\Configuration\Passes\ProcessingPassInterface    ...$passes
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
