<?php declare(strict_types=1);

namespace Quanta\Container;

final class Configuration implements ConfigurationInterface
{
    /**
     * The processed factory map to provide.
     *
     * @var \Quanta\Container\ConfiguredFactoryMap
     */
    private $map;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\ConfiguredFactoryMap $map
     */
    public function __construct(ConfiguredFactoryMap $map)
    {
        $this->map = $map;
    }

    /**
     * @inheritdoc
     */
    public function map(): ConfiguredFactoryMap
    {
        return $this->map;
    }
}
