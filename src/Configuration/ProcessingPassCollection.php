<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\FactoryMap;
use Quanta\Container\ProcessingPassInterface;

final class ProcessingPassCollection implements ConfigurationInterface
{
    /**
     * The array of processing passes to provide.
     *
     * @var \Quanta\Container\ProcessingPassInterface[]
     */
    private $passes;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\ProcessingPassInterface ...$passes
     */
    public function __construct(ProcessingPassInterface ...$passes)
    {
        $this->passes = $passes;
    }

    /**
     * @inheritdoc
     */
    public function unit(): ConfigurationUnitInterface
    {
        return new ConfigurationUnit(new FactoryMap([]), ...$this->passes);
    }
}
