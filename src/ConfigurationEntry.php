<?php declare(strict_types=1);

namespace Quanta\Container;

use Quanta\Container\Passes\ConfigurationPassInterface;

final class ConfigurationEntry implements ConfigurationEntryInterface
{
    /**
     * The factories provided by the configuration entry.
     *
     * @var \Quanta\Container\FactoryMapInterface
     */
    private $factories;

    /**
     * The extensions provided by the configuration entry.
     *
     * @var \Quanta\Container\FactoryMapInterface
     */
    private $extensions;

    /**
     * The metadata associated to the factories.
     *
     * @var array[]
     */
    private $metadata;

    /**
     * The configuration passes provided by the configuration entry.
     *
     * @var \Quanta\Container\Passes\ConfigurationPassInterface[]
     */
    private $passes;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\FactoryMapInterface                 $factories
     * @param \Quanta\Container\FactoryMapInterface                 $extensions
     * @param array[]                                               $metadata
     * @param \Quanta\Container\Passes\ConfigurationPassInterface   ...$passes
     */
    public function __construct(
        FactoryMapInterface $factories,
        FactoryMapInterface $extensions,
        array $metadata = [],
        ConfigurationPassInterface ...$passes
    ) {
        $this->factories = $factories;
        $this->extensions = $extensions;
        $this->metadata = $metadata;
        $this->passes = $passes;
    }

    /**
     * @inheritdoc
     */
    public function factories(): FactoryMapInterface
    {
        return $this->factories;
    }

    /**
     * @inheritdoc
     */
    public function extensions(): FactoryMapInterface
    {
        return $this->extensions;
    }

    /**
     * @inheritdoc
     */
    public function metadata(): array
    {
        return $this->metadata;
    }

    /**
     * @inheritdoc
     */
    public function passes(): array
    {
        return $this->passes;
    }
}
