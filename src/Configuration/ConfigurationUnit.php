<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\MergedProcessingPass;
use Quanta\Container\ProcessingPassInterface;

final class ConfigurationUnit implements ConfigurationUnitInterface
{
    /**
     * The associative array of factories.
     *
     * @var callable[]
     */
    private $factories;

    /**
     * The array of processing passes.
     *
     * @var \Quanta\Container\ProcessingPassInterface[]
     */
    private $passes;

    /**
     * Constructor.
     *
     * @param callable[]                                $factories
     * @param \Quanta\Container\ProcessingPassInterface ...$passes
     * @throws \InvalidArgumentException
     */
    public function __construct(array $factories, ProcessingPassInterface ...$passes)
    {
        $result = \Quanta\ArrayTypeCheck::result($factories, 'callable');

        if (! $result->isValid()) {
            throw new \InvalidArgumentException(
                $result->message()->constructor($this, 1)
            );
        }

        $this->factories = $factories;
        $this->passes = $passes;
    }

    /**
     * @inheritdoc
     */
    public function factories(): array
    {
        return $this->factories;
    }

    /**
     * @inheritdoc
     */
    public function pass(): ProcessingPassInterface
    {
        return new MergedProcessingPass(...$this->passes);
    }
}
