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
     * The tags describing the factories provided by the configuration entry.
     *
     * @var array[]
     */
    private $tags;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\FactoryMapInterface $factories
     * @param \Quanta\Container\FactoryMapInterface $extensions
     * @param array[]                               $tags
     */
    public function __construct(
        FactoryMapInterface $factories,
        FactoryMapInterface $extensions,
        array $tags
    ) {
        $this->factories = $factories;
        $this->extensions = $extensions;
        $this->tags = $tags;
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
    public function tags(): array
    {
        return $this->tags;
    }
}
