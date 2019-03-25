<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\EmptyFactoryMap;
use Quanta\Container\Configuration\Passes\MergedProcessingPass;
use Quanta\Container\Configuration\Passes\ProcessingPassInterface;

final class ProcessingPassCollection implements ConfigurationSourceInterface
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
    public function configuration(): ConfigurationInterface
    {
        return new Configuration(
            new EmptyFactoryMap,
            new MergedProcessingPass(...$this->passes)
        );
    }
}
