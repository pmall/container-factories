<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\EmptyFactoryMap;
use Quanta\Container\Configuration\Passes\ProcessingPassInterface;

final class ProcessingPassCollection implements ConfigurationInterface
{
    /**
     * The array of processing passes to provide.
     *
     * @var \Quanta\Container\Configuration\Passes\ProcessingPassInterface[]
     */
    private $passes;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\Configuration\Passes\ProcessingPassInterface ...$passes
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
        return new ConfigurationUnit(new EmptyFactoryMap, ...$this->passes);
    }
}
