<?php declare(strict_types=1);

namespace Quanta\Container;

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
     * Constructor.
     *
     * @param \Quanta\Container\FactoryMapInterface $factories
     * @param \Quanta\Container\FactoryMapInterface $extensions
     * @param array[]                               $metadata
     */
    public function __construct(
        FactoryMapInterface $factories,
        FactoryMapInterface $extensions,
        array $metadata = []
    ) {
        $this->factories = $factories;
        $this->extensions = $extensions;
        $this->metadata = $metadata;
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
}
