<?php declare(strict_types=1);

namespace Quanta\Container\Configuration;

use Quanta\Container\ProcessedFactoryMap;

final class Configuration implements ConfigurationInterface
{
    /**
     * The processed factory map to provide.
     *
     * @var \Quanta\Container\ProcessedFactoryMap
     */
    private $map;

    /**
     * Constructor.
     *
     * @param \Quanta\Container\ProcessedFactoryMap $map
     */
    public function __construct(ProcessedFactoryMap $map)
    {
        $this->map = $map;
    }

    /**
     * @inheritdoc
     */
    public function map(): ProcessedFactoryMap
    {
        return $this->map;
    }
}
